<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeLeave;
use App\Models\GraceSetting;
use App\Models\Reimbursement;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\Salarytype;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalarySummary;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class PayrollCalculatorService
{
    /**
     * Compute comprehensive, market-standard payroll for an employee.
     *
     * @param Employee $employee
     * @param int $month (1-12)
     * @param int $year
     * @param float|null $overrideBasic
     * @param array $options
     * @return array
     */
    public function calculate(Employee $employee, int $month, int $year, $overrideBasic = null, array $options = []): array
    {
        $basicSalary = $overrideBasic !== null && is_numeric($overrideBasic)
            ? (float)$overrideBasic
            : (float)($employee->salary ?? 0);

        $departmentName = $employee->department ? $employee->department->name : 'General';
        $companyId = $employee->company_id;

        // 1. Month boundaries
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $monthStart->daysInMonth;

        // 2. Mid-month Proration Handling (Joining & Leaving Dates)
        $joiningDate = $employee->date_of_joining ? Carbon::parse($employee->date_of_joining)->startOfDay() : null;
        $leavingDate = $employee->date_of_leaving ? Carbon::parse($employee->date_of_leaving)->endOfDay() : null;

        $hasJoined = !$joiningDate || $joiningDate->lte($monthEnd);
        $hasLeft = $leavingDate && $leavingDate->lt($monthStart);

        if (!$hasJoined || $hasLeft) {
            // Employee was inactive for the entire month
            return $this->getZeroPayrollResult($employee, $departmentName, $basicSalary, $daysInMonth);
        }

        $effectiveStart = ($joiningDate && $joiningDate->gt($monthStart)) ? $joiningDate->copy() : $monthStart->copy();
        $effectiveEnd = ($leavingDate && $leavingDate->lt($monthEnd)) ? $leavingDate->copy() : $monthEnd->copy();

        $tenureCalendarDays = $effectiveStart->diffInDays($effectiveEnd) + 1;
        $isProrated = ($tenureCalendarDays < $daysInMonth);

        // 3. Shift Week-offs
        $weekOffDays = $this->getEmployeeWeekOffs($employee);

        // 4. Company Holidays in Month
        $companyHolidays = $this->getCompanyHolidayDates($companyId, $year, $month);

        // 5. Working Days calculation (Full Month vs Active Tenure)
        $allMonthDates = CarbonPeriod::create($monthStart, $monthEnd);
        $totalMonthWorkingDays = 0;
        foreach ($allMonthDates as $date) {
            $dayOfWeek = $date->dayOfWeek + 1; // 1 = Sun, 7 = Sat
            if (!in_array($dayOfWeek, $weekOffDays) && !$companyHolidays->contains($date->format('Y-m-d'))) {
                $totalMonthWorkingDays++;
            }
        }
        $totalMonthWorkingDays = max(1, $totalMonthWorkingDays);

        // Active tenure working days (where employee was actually employed)
        $tenurePeriod = CarbonPeriod::create($effectiveStart, $effectiveEnd);
        $tenureWorkingDays = 0;
        $tenureHolidays = 0;
        foreach ($tenurePeriod as $date) {
            $dayOfWeek = $date->dayOfWeek + 1;
            $dateStr = $date->format('Y-m-d');
            if ($companyHolidays->contains($dateStr)) {
                $tenureHolidays++;
            } elseif (!in_array($dayOfWeek, $weekOffDays)) {
                $tenureWorkingDays++;
            }
        }
        $tenureWorkingDays = max(1, $tenureWorkingDays);
        $workingDays = $isProrated ? $tenureWorkingDays : $totalMonthWorkingDays;

        // 6. Grace Settings & Shift Start Time
        $shiftStartTime = '10:00:00';
        if ($employee->shifts && count($employee->shifts) > 0) {
            foreach ($employee->shifts as $shift) {
                if (!empty($shift->start_time)) {
                    $shiftStartTime = $shift->start_time;
                    break;
                }
            }
        }

        $graceSettings = GraceSetting::where('company_id', $companyId)->get();
        $graceTimeMinutes = (isset($graceSettings[0]) && is_numeric($graceSettings[0]->value)) ? (int)$graceSettings[0]->value : 15;
        $graceAllowedDays = (isset($graceSettings[1]) && is_numeric($graceSettings[1]->value) && (int)$graceSettings[1]->value > 0) ? (int)$graceSettings[1]->value : 3;
        $graceCutoffEntry = Carbon::parse($shiftStartTime)->addMinutes($graceTimeMinutes)->format('H:i:s');

        // 7. Attendance Scan (strictly within active tenure)
        $attendances = Attendance::where('employee_id', $employee->id)
            ->where('company_id', $companyId)
            ->whereBetween('date', [$effectiveStart->format('Y-m-d'), $effectiveEnd->format('Y-m-d')])
            ->get();

        $fullPresentCount = 0;
        $halfDayCount = 0;
        $lateArrivalCount = 0;
        $attendanceLeaveCount = 0;

        foreach ($attendances as $att) {
            $attType = strtolower(trim((string)$att->attendance));
            $isHalf = ((int)$att->halfday === 1) || in_array($attType, ['halfday', 'half day']);

            if ($isHalf) {
                $halfDayCount++;
            } elseif ($attType === 'present') {
                $fullPresentCount++;
                if (!empty($att->in_time) && $att->in_time > $graceCutoffEntry) {
                    $lateArrivalCount++;
                }
            } elseif ($attType === 'leave') {
                $attendanceLeaveCount++;
            }
        }

        // Approved Leaves
        $approvedLeaves = (float)EmployeeLeave::where('employee_id', $employee->id)
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->where(function ($q) use ($effectiveStart, $effectiveEnd) {
                $q->whereBetween('from_date', [$effectiveStart->format('Y-m-d'), $effectiveEnd->format('Y-m-d')])
                  ->orWhereBetween('to_date', [$effectiveStart->format('Y-m-d'), $effectiveEnd->format('Y-m-d')]);
            })
            ->sum('days');

        $leaveDays = max($approvedLeaves, (float)$attendanceLeaveCount);
        $presentDays = round($fullPresentCount + ($halfDayCount * 0.5), 1);

        // Effective Paid Days & Absent / LOP Days
        // Note: Paid days = Present + Approved Leaves (cannot exceed tenure working days)
        $paidDays = min($workingDays, round($presentDays + $leaveDays, 1));
        $absentDays = max(0, round($workingDays - $paidDays, 1));

        // Late penalty days (e.g. 3 lates = 1 day penalty)
        $latePenaltyDays = $graceAllowedDays > 0 ? (int)floor($lateArrivalCount / $graceAllowedDays) : 0;

        // 8. Daily Salary Divisor (Calendar Days vs Working Days vs Fixed 30)
        $divisorMode = $options['divisor_mode'] ?? 'calendar_days';
        $divisor = match ($divisorMode) {
            'working_days' => $totalMonthWorkingDays,
            'fixed_30'     => 30,
            default        => $daysInMonth,
        };
        $dailySalary = $basicSalary > 0 ? round($basicSalary / max(1, $divisor), 2) : 0;

        // 9. Attendance Deductions (LOP)
        // If employee is prorated (joined mid-month), base earnings scale by active days
        $prorationFactor = $isProrated ? ($tenureCalendarDays / $daysInMonth) : 1.0;
        $absentDeduction = round($absentDays * $dailySalary, 2);
        $lateDeduction = round($latePenaltyDays * $dailySalary, 2);
        $halfDayDeduction = round($halfDayCount * ($dailySalary / 2), 2);
        $otherDeduction = round($absentDeduction + $lateDeduction, 2);

        // 10. Approved Reimbursements / Expenses
        $reimbursement = (float)Expense::where('employee_id', $employee->id)
            ->where('company_id', $companyId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'Approved')
            ->sum('amount');

        if ($reimbursement <= 0) {
            $reimbursement = (float)Reimbursement::where('employee_id', $employee->id)
                ->where('company_id', $companyId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('status', 'Approved')
                ->sum('amount');
        }

        // 11. Salary Components (Earnings & Deductions from Salarytype)
        $salarytypes = $this->getSalaryComponentsForEmployee($employee);

        $earnings = [];
        $deductions = [];
        $totalEarningComponents = 0;
        $totalDeductionComponents = 0;

        $hasPfComponent = false;
        $hasEsiComponent = false;
        $hasPtComponent = false;

        foreach ($salarytypes as $st) {
            $amount = ($st->amount_type === 'Flat')
                ? (float)$st->amount
                : round(($basicSalary * (float)$st->amount / 100), 2);

            $label = $st->salary_type . ($st->amount_type !== 'Flat' ? ' (' . $st->amount . '%)' : '');
            $item = [
                'id' => $st->id,
                'label' => $label,
                'name' => $st->salary_type,
                'amount' => $amount,
                'amount_type' => $st->amount_type,
                'rate' => $st->amount,
                'payment_type' => $st->payment_type,
            ];

            if ($st->payment_type === 'Earning') {
                $earnings[] = $item;
                $totalEarningComponents += $amount;
            } else {
                $deductions[] = $item;
                $totalDeductionComponents += $amount;

                // Check for statutory labels
                $lowerName = strtolower($st->salary_type);
                if (str_contains($lowerName, 'pf') || str_contains($lowerName, 'provident')) $hasPfComponent = true;
                if (str_contains($lowerName, 'esi') || str_contains($lowerName, 'insurance')) $hasEsiComponent = true;
                if (str_contains($lowerName, 'pt') || str_contains($lowerName, 'professional tax')) $hasPtComponent = true;
            }
        }

        $grossEarnings = round($basicSalary + $totalEarningComponents + $reimbursement, 2);

        // 12. Indian Statutory Compliance Engine (EPF, ESIC, PT)
        // If not already configured as a custom deduction component:
        $statutory = $this->computeStatutoryDeductions(
            $employee,
            $basicSalary,
            $grossEarnings,
            $hasPfComponent,
            $hasEsiComponent,
            $hasPtComponent,
            $month,
            $options
        );

        $pfEmployee = $statutory['pf_employee'];
        $pfEmployer = $statutory['pf_employer'];
        $esiEmployee = $statutory['esi_employee'];
        $esiEmployer = $statutory['esi_employer'];
        $ptAmount = $statutory['pt_amount'];
        $tdsAmount = (float)($options['tds_amount'] ?? 0);

        // Include any statutory items that were auto-computed into deductions list
        foreach ($statutory['auto_deductions'] as $autoDed) {
            $deductions[] = $autoDed;
            $totalDeductionComponents += $autoDed['amount'];
        }

        $totalStatutoryDeductions = round($pfEmployee + $esiEmployee + $ptAmount + $tdsAmount, 2);
        $totalDeductions = round($totalDeductionComponents + $otherDeduction + $tdsAmount, 2);
        $netSalary = max(0, round($grossEarnings - $totalDeductions, 2));

        return [
            'employee' => $employee,
            'department_name' => $departmentName,
            'basic_salary' => $basicSalary,
            'daily_salary' => $dailySalary,
            'days_in_month' => $daysInMonth,
            'is_prorated' => $isProrated,
            'tenure_calendar_days' => $tenureCalendarDays,
            'total_working_days' => $totalMonthWorkingDays,
            'holiday_count' => $companyHolidays->count(),
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'leave_days' => $leaveDays,
            'absent_days' => $absentDays,
            'absent_count' => $absentDays,
            'late_count' => $lateArrivalCount,
            'halfday_count' => $halfDayCount,
            'absent_deduction' => $absentDeduction,
            'late_deduction' => $lateDeduction,
            'halfday_deduction' => $halfDayDeduction,
            'other_deduction' => $otherDeduction,
            'reimbursement' => $reimbursement,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'total_earning_components' => round($totalEarningComponents, 2),
            'total_deduction_components' => round($totalDeductionComponents, 2),
            'pf_employee' => $pfEmployee,
            'pf_employer' => $pfEmployer,
            'esi_employee' => $esiEmployee,
            'esi_employer' => $esiEmployer,
            'pt_amount' => $ptAmount,
            'tds_amount' => $tdsAmount,
            'gross_earnings' => $grossEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'salarytypes' => $salarytypes,
        ];
    }

    /**
     * Indian Statutory Deductions Engine (EPF, ESIC, Professional Tax)
     */
    protected function computeStatutoryDeductions(
        Employee $employee,
        float $basicSalary,
        float $grossEarnings,
        bool $hasPfComponent,
        bool $hasEsiComponent,
        bool $hasPtComponent,
        int $month,
        array $options
    ): array {
        $pfEmployee = 0.0;
        $pfEmployer = 0.0;
        $esiEmployee = 0.0;
        $esiEmployer = 0.0;
        $ptAmount = 0.0;
        $autoDeductions = [];

        $enablePf = $options['enable_pf'] ?? (!empty($employee->pf_number));
        $enableEsi = $options['enable_esi'] ?? (!empty($employee->esi_number) || ($grossEarnings > 0 && $grossEarnings <= 21000));
        $enablePt = $options['enable_pt'] ?? true;

        // 1. EPF Calculation
        if ($enablePf && !$hasPfComponent && $basicSalary > 0) {
            $pfWageLimit = 15000.0;
            $pfCap = $options['pf_cap'] ?? true;
            $eligiblePfWage = $pfCap ? min($basicSalary, $pfWageLimit) : $basicSalary;

            $pfEmployee = round($eligiblePfWage * 0.12, 2);
            $epsEmployer = round($eligiblePfWage * 0.0833, 2);
            if ($epsEmployer > 1250) $epsEmployer = 1250.0;
            $epfEmployer = max(0, round($pfEmployee - $epsEmployer, 2));
            $pfEmployer = round($epsEmployer + $epfEmployer, 2);

            $autoDeductions[] = [
                'id' => null,
                'label' => 'Provident Fund (PF - 12%)',
                'name' => 'Provident Fund (PF)',
                'amount' => $pfEmployee,
                'amount_type' => 'Statutory',
                'rate' => 12,
                'payment_type' => 'Deduction',
            ];
        }

        // 2. ESIC Calculation (Standard Gross Wage <= ₹21,000)
        if ($enableEsi && !$hasEsiComponent && $grossEarnings > 0 && $grossEarnings <= 21000) {
            $esiEmployee = round($grossEarnings * 0.0075, 2);
            $esiEmployer = round($grossEarnings * 0.0325, 2);

            $autoDeductions[] = [
                'id' => null,
                'label' => 'ESIC (0.75%)',
                'name' => 'ESIC',
                'amount' => $esiEmployee,
                'amount_type' => 'Statutory',
                'rate' => 0.75,
                'payment_type' => 'Deduction',
            ];
        }

        // 3. Professional Tax (PT) - Indian State Standard
        if ($enablePt && !$hasPtComponent && $grossEarnings >= 7500) {
            // Standard slab: 7500-10000 = 175, >10000 = 200 (300 in Feb)
            if ($grossEarnings > 10000) {
                $ptAmount = ($month === 2) ? 300.0 : 200.0;
            } elseif ($grossEarnings >= 7500) {
                $ptAmount = 175.0;
            }

            if ($ptAmount > 0) {
                $autoDeductions[] = [
                    'id' => null,
                    'label' => 'Professional Tax (PT)',
                    'name' => 'Professional Tax',
                    'amount' => $ptAmount,
                    'amount_type' => 'Statutory',
                    'rate' => $ptAmount,
                    'payment_type' => 'Deduction',
                ];
            }
        }

        return [
            'pf_employee' => $pfEmployee,
            'pf_employer' => $pfEmployer,
            'esi_employee' => $esiEmployee,
            'esi_employer' => $esiEmployer,
            'pt_amount' => $ptAmount,
            'auto_deductions' => $autoDeductions,
        ];
    }

    /**
     * Save canonical payroll line items and summary.
     */
    public function savePayrollRecord(int $companyId, int $employeeId, int $month, int $year, array $calc, ?string $status = 'Generated'): EmployeeSalarySummary
    {
        // 1. Check if payroll record exists and is locked
        $existing = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->first();

        if ($existing && $existing->is_locked) {
            throw new \RuntimeException('Cannot overwrite payroll: this cycle has been finalized and locked.');
        }

        // 2. Clean previous component items for this employee & cycle
        EmployeeSalary::where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->delete();

        // 3. Save earnings and deductions components
        $allComponents = array_merge($calc['earnings'] ?? [], $calc['deductions'] ?? []);

        if (!empty($allComponents)) {
            foreach ($allComponents as $comp) {
                $salary = new EmployeeSalary();
                $salary->company_id = $companyId;
                $salary->employee_id = $employeeId;
                $salary->salary_month = $month;
                $salary->salary_year = $year;
                $salary->salary_type_id = $comp['id'] ?? null;
                $salary->salary_type_amount = $comp['rate'] ?? $comp['amount'];
                $salary->salary_type_amount_type = $comp['amount_type'] ?? 'Flat';
                $salary->payment_type = $comp['payment_type'] ?? 'Earning';
                $salary->amount = $comp['amount'];
                $salary->label = $comp['label'] ?? ($comp['name'] ?? 'Salary Item');
                $salary->basic_salary = $calc['basic_salary'];
                $salary->gross_salary = $calc['gross_earnings'];
                $salary->other_deduction = $calc['other_deduction'];
                $salary->absent_days = $calc['absent_days'];
                $salary->working_days = $calc['working_days'];
                $salary->reimbursement = $calc['reimbursement'];
                $salary->save();
            }
        } else {
            // Save base salary component if no components configured
            $salary = new EmployeeSalary();
            $salary->company_id = $companyId;
            $salary->employee_id = $employeeId;
            $salary->salary_month = $month;
            $salary->salary_year = $year;
            $salary->salary_type_id = null;
            $salary->salary_type_amount = $calc['basic_salary'];
            $salary->salary_type_amount_type = 'Flat';
            $salary->payment_type = 'Earning';
            $salary->amount = $calc['basic_salary'];
            $salary->label = 'Basic Salary';
            $salary->basic_salary = $calc['basic_salary'];
            $salary->gross_salary = $calc['gross_earnings'];
            $salary->other_deduction = $calc['other_deduction'];
            $salary->absent_days = $calc['absent_days'];
            $salary->working_days = $calc['working_days'];
            $salary->reimbursement = $calc['reimbursement'];
            $salary->save();
        }

        // 4. Update or Create EmployeeSalarySummary
        return EmployeeSalarySummary::updateOrCreate(
            [
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'salary_month' => $month,
                'salary_year' => $year,
            ],
            [
                'employee_name' => $calc['employee']->name ?? 'Employee',
                'department_name' => $calc['department_name'] ?? 'General',
                'basic_salary' => $calc['basic_salary'],
                'total_earning' => $calc['total_earning_components'],
                'total_deduction' => $calc['total_deduction_components'],
                'other_deduction' => $calc['other_deduction'],
                'reimbursement' => $calc['reimbursement'],
                'net_salary' => $calc['net_salary'],
                'gross_salary' => $calc['gross_earnings'],
                'absent_days' => $calc['absent_days'],
                'working_days' => $calc['working_days'],
                'total_working_days' => $calc['total_working_days'],
                'holiday_count' => $calc['holiday_count'],
                'present_days' => $calc['present_days'],
                'leave_days' => $calc['leave_days'],
                'late_count' => $calc['late_count'],
                'halfday_count' => $calc['halfday_count'],
                'pf_employee' => $calc['pf_employee'],
                'pf_employer' => $calc['pf_employer'],
                'esi_employee' => $calc['esi_employee'],
                'esi_employer' => $calc['esi_employer'],
                'pt_amount' => $calc['pt_amount'],
                'tds_amount' => $calc['tds_amount'],
                'status' => $status ?? 'Generated',
                'payment_status' => $existing ? $existing->payment_status : 'Pending',
                'generated_date' => now(),
            ]
        );
    }

    /**
     * Return week-off days (1 = Sunday, 7 = Saturday).
     */
    protected function getEmployeeWeekOffs(Employee $employee): array
    {
        $weekOffs = [];
        if ($employee->shifts && count($employee->shifts) > 0) {
            foreach ($employee->shifts as $shift) {
                if ($shift->sunday == 0) $weekOffs[] = 1;
                if ($shift->monday == 0) $weekOffs[] = 2;
                if ($shift->tuesday == 0) $weekOffs[] = 3;
                if ($shift->wednesday == 0) $weekOffs[] = 4;
                if ($shift->thursday == 0) $weekOffs[] = 5;
                if ($shift->friday == 0) $weekOffs[] = 6;
                if ($shift->saturday == 0) $weekOffs[] = 7;
            }
        }
        return empty($weekOffs) ? [1] : array_unique($weekOffs);
    }

    /**
     * Get unique holiday dates for company in given month.
     */
    public function getCompanyHolidayDates(int $companyId, int $year, int $month): Collection
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $holidays = Holiday::where('company_id', $companyId)
            ->whereDate('end_date', '>=', $startOfMonth)
            ->whereDate('start_date', '<=', $endOfMonth)
            ->get();

        $holidayDates = collect();
        foreach ($holidays as $holiday) {
            $hStart = Carbon::parse($holiday->start_date)->greaterThan($startOfMonth) ? Carbon::parse($holiday->start_date) : $startOfMonth;
            $hEnd = Carbon::parse($holiday->end_date)->lessThan($endOfMonth) ? Carbon::parse($holiday->end_date) : $endOfMonth;

            foreach (CarbonPeriod::create($hStart, $hEnd) as $date) {
                $holidayDates->push($date->format('Y-m-d'));
            }
        }
        return $holidayDates->unique();
    }

    /**
     * Fetch salary types applicable for employee.
     */
    protected function getSalaryComponentsForEmployee(Employee $employee): Collection
    {
        $salarytypes = collect();
        if ($employee->department_id) {
            $salarytypes = Salarytype::where('company_id', $employee->company_id)
                ->where('department_id', $employee->department_id)
                ->where('status', '1')
                ->get();
        }

        if ($salarytypes->isEmpty()) {
            $salarytypes = Salarytype::where('company_id', $employee->company_id)
                ->where(function ($q) {
                    $q->whereNull('department_id')->orWhere('department_id', 0);
                })
                ->where('status', '1')
                ->get();
        }

        return $salarytypes;
    }

    /**
     * Zero result fallback for employees not active in month.
     */
    protected function getZeroPayrollResult(Employee $employee, string $departmentName, float $basicSalary, int $daysInMonth): array
    {
        return [
            'employee' => $employee,
            'department_name' => $departmentName,
            'basic_salary' => $basicSalary,
            'daily_salary' => 0.0,
            'days_in_month' => $daysInMonth,
            'is_prorated' => true,
            'tenure_calendar_days' => 0,
            'total_working_days' => 0,
            'holiday_count' => 0,
            'working_days' => 0,
            'present_days' => 0.0,
            'leave_days' => 0.0,
            'absent_days' => 0.0,
            'absent_count' => 0.0,
            'late_count' => 0,
            'halfday_count' => 0.0,
            'absent_deduction' => 0.0,
            'late_deduction' => 0.0,
            'halfday_deduction' => 0.0,
            'other_deduction' => 0.0,
            'reimbursement' => 0.0,
            'earnings' => [],
            'deductions' => [],
            'total_earning_components' => 0.0,
            'total_deduction_components' => 0.0,
            'pf_employee' => 0.0,
            'pf_employer' => 0.0,
            'esi_employee' => 0.0,
            'esi_employer' => 0.0,
            'pt_amount' => 0.0,
            'tds_amount' => 0.0,
            'gross_earnings' => 0.0,
            'total_deductions' => 0.0,
            'net_salary' => 0.0,
            'salarytypes' => collect(),
        ];
    }
}
