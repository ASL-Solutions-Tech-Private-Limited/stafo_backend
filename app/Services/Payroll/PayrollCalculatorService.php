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
     * Compute comprehensive, market-standard payroll for an employee (Keka / Zoho Payroll parity).
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
        $annualCtc = (float)($employee->ctc ?? 0);
        $monthlyCtc = $annualCtc > 0 ? round($annualCtc / 12, 2) : 0.0;

        $basicSalary = $overrideBasic !== null && is_numeric($overrideBasic)
            ? (float)$overrideBasic
            : (float)($employee->salary ?? 0);

        // If basic salary is not set but CTC is set, Keka standard defaults Basic to 50% of monthly CTC
        if ($basicSalary <= 0 && $monthlyCtc > 0) {
            $basicSalary = round($monthlyCtc * 0.50, 2);
        }

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
        $attendanceByDate = [];

        foreach ($attendances as $att) {
            $attType = strtolower(trim((string)$att->attendance));
            $isHalf = ((int)$att->halfday === 1) || in_array($attType, ['halfday', 'half day']);
            $attDate = Carbon::parse($att->date)->format('Y-m-d');
            $attendanceByDate[$attDate] = $attType;

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
        $divisorMode = $options['divisor_mode'] ?? 'calendar_days';
        $divisor = match ($divisorMode) {
            'working_days' => $totalMonthWorkingDays,
            'fixed_30'     => 30,
            default        => $daysInMonth,
        };

        $baseTenurePaid = round($presentDays + $leaveDays, 1);

        if ($isProrated) {
            // In Keka & Zoho, unworked days outside active tenure count as non-paid/LOP
            if ($divisorMode === 'working_days') {
                $nonTenureUnpaidDays = max(0, $totalMonthWorkingDays - $tenureWorkingDays);
                $tenureAbsent = max(0, round($tenureWorkingDays - $baseTenurePaid, 1));
                $absentDays = round($nonTenureUnpaidDays + $tenureAbsent, 1);
                $paidDays = max(0, round($totalMonthWorkingDays - $absentDays, 1));
                $workingDays = $totalMonthWorkingDays;
            } else {
                // Calendar mode: tenure calendar days vs total month days
                $nonTenureUnpaidDays = max(0, $daysInMonth - $tenureCalendarDays);
                $tenureAbsent = max(0, round($tenureWorkingDays - $baseTenurePaid, 1));
                $absentDays = round($nonTenureUnpaidDays + $tenureAbsent, 1);
                $paidDays = max(0, round($daysInMonth - $absentDays, 1));
                $workingDays = $daysInMonth;
            }
        } else {
            $paidDays = min($workingDays, $baseTenurePaid);
            $absentDays = max(0, round($workingDays - $paidDays, 1));
        }

        // Late penalty days (e.g. 3 lates = 1 day penalty)
        $latePenaltyDays = $graceAllowedDays > 0 ? (int)floor($lateArrivalCount / $graceAllowedDays) : 0;

        // Daily Salary Divisor
        $dailySalary = $basicSalary > 0 ? round($basicSalary / max(1, $divisor), 2) : 0;

        // 9. Attendance Deductions (LOP)
        $absentDeduction = round($absentDays * $dailySalary, 2);
        $lateDeduction = round($latePenaltyDays * $dailySalary, 2);
        $halfDayDeduction = round($halfDayCount * ($dailySalary / 2), 2);

        // 10. Sandwich Leave Rule Check (Keka Standard)
        $enableSandwich = $options['enable_sandwich'] ?? false;
        $sandwichDeduction = 0.0;
        $sandwichDays = 0;
        if ($enableSandwich && $dailySalary > 0) {
            $sandwichDays = $this->detectSandwichLeaveDays($effectiveStart, $effectiveEnd, $weekOffDays, $companyHolidays, $attendanceByDate);
            $sandwichDeduction = round($sandwichDays * $dailySalary, 2);
        }

        $otherDeduction = round($absentDeduction + $lateDeduction + $sandwichDeduction, 2);

        // 11. Approved Reimbursements / Expenses
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

        // 12. Arrears & Performance Bonus (Keka / Zoho Standard)
        $arrears = (float)($options['arrears'] ?? 0.0);
        $bonus = (float)($options['bonus'] ?? 0.0);

        // 13. Salary Components (Earnings & Deductions)
        $salarytypes = $this->getSalaryComponentsForEmployee($employee);

        $earnings = [];
        $deductions = [];
        $totalEarningComponents = 0;
        $totalDeductionComponents = 0;

        $hasPfComponent = false;
        $hasEsiComponent = false;
        $hasPtComponent = false;

        if ($salarytypes->isNotEmpty()) {
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
        } elseif ($monthlyCtc > 0 && $basicSalary > 0) {
            // Enterprise CTC Auto-Decomposition Structure (Keka / Zoho standard breakdown)
            $hraAmount = round($basicSalary * 0.40, 2); // 40% of Basic HRA
            $conveyanceAmount = 1600.0; // Standard Conveyance Allowance
            $medicalAmount = 1250.0;    // Standard Medical Allowance

            $earnings[] = [
                'id' => null,
                'label' => 'House Rent Allowance (HRA - 40%)',
                'name' => 'House Rent Allowance (HRA)',
                'amount' => $hraAmount,
                'amount_type' => 'Percentage',
                'rate' => 40,
                'payment_type' => 'Earning',
            ];
            $earnings[] = [
                'id' => null,
                'label' => 'Conveyance Allowance',
                'name' => 'Conveyance Allowance',
                'amount' => $conveyanceAmount,
                'amount_type' => 'Flat',
                'rate' => $conveyanceAmount,
                'payment_type' => 'Earning',
            ];
            $earnings[] = [
                'id' => null,
                'label' => 'Medical Allowance',
                'name' => 'Medical Allowance',
                'amount' => $medicalAmount,
                'amount_type' => 'Flat',
                'rate' => $medicalAmount,
                'payment_type' => 'Earning',
            ];

            // Employer PF & Gratuity estimates for balancing special allowance
            $estimatedPfEmployer = min($basicSalary, 15000.0) * 0.12;
            $estimatedGratuity = round((15 / 26) * ($basicSalary / 12), 2);
            $specialAllowance = max(0, round($monthlyCtc - ($basicSalary + $hraAmount + $conveyanceAmount + $medicalAmount + $estimatedPfEmployer + $estimatedGratuity), 2));

            if ($specialAllowance > 0) {
                $earnings[] = [
                    'id' => null,
                    'label' => 'Special Allowance',
                    'name' => 'Special Allowance',
                    'amount' => $specialAllowance,
                    'amount_type' => 'Flat',
                    'rate' => $specialAllowance,
                    'payment_type' => 'Earning',
                ];
            }

            foreach ($earnings as $e) {
                $totalEarningComponents += $e['amount'];
            }
        }

        // Add Arrears and Bonus into Earnings list if present
        if ($arrears > 0) {
            $earnings[] = [
                'id' => null,
                'label' => 'Salary Arrears',
                'name' => 'Arrears',
                'amount' => $arrears,
                'amount_type' => 'Flat',
                'rate' => $arrears,
                'payment_type' => 'Earning',
            ];
            $totalEarningComponents += $arrears;
        }

        if ($bonus > 0) {
            $earnings[] = [
                'id' => null,
                'label' => 'Performance / Variable Bonus',
                'name' => 'Bonus',
                'amount' => $bonus,
                'amount_type' => 'Flat',
                'rate' => $bonus,
                'payment_type' => 'Earning',
            ];
            $totalEarningComponents += $bonus;
        }

        $grossEarnings = round($basicSalary + $totalEarningComponents + $reimbursement, 2);

        // 14. Indian Statutory Compliance Engine (EPF, ESIC, PT, Gratuity)
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
        $gratuity = $statutory['gratuity'];

        // 15. Automated Income Tax / TDS Engine (Old vs New Regime)
        $taxRegime = $employee->tax_regime ?? ($options['tax_regime'] ?? 'new');
        $tdsDetails = $this->computeAutomatedTds($employee, $grossEarnings, $month, $year, $taxRegime, $options);
        $tdsAmount = $tdsDetails['monthly_tds'];

        // Include any statutory items that were auto-computed into deductions list
        foreach ($statutory['auto_deductions'] as $autoDed) {
            $deductions[] = $autoDed;
            $totalDeductionComponents += $autoDed['amount'];
        }

        if ($tdsAmount > 0) {
            $deductions[] = [
                'id' => null,
                'label' => 'Tax Deducted at Source (TDS - ' . strtoupper($taxRegime) . ' Regime)',
                'name' => 'TDS',
                'amount' => $tdsAmount,
                'amount_type' => 'Statutory',
                'rate' => $tdsAmount,
                'payment_type' => 'Deduction',
            ];
            $totalDeductionComponents += $tdsAmount;
        }

        $totalStatutoryDeductions = round($pfEmployee + $esiEmployee + $ptAmount + $tdsAmount, 2);
        $totalDeductions = round($totalDeductionComponents + $otherDeduction, 2);
        $netSalary = max(0, round($grossEarnings - $totalDeductions, 2));

        // Effective Monthly CTC
        $effectiveMonthlyCtc = $monthlyCtc > 0
            ? $monthlyCtc
            : round($grossEarnings + $pfEmployer + $esiEmployer + $gratuity, 2);

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
            'sandwich_deduction' => $sandwichDeduction,
            'other_deduction' => $otherDeduction,
            'reimbursement' => $reimbursement,
            'arrears' => $arrears,
            'bonus' => $bonus,
            'ctc' => $effectiveMonthlyCtc,
            'annual_ctc' => $annualCtc > 0 ? $annualCtc : round($effectiveMonthlyCtc * 12, 2),
            'gratuity' => $gratuity,
            'tax_regime' => $taxRegime,
            'tax_details' => $tdsDetails,
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
     * Indian Statutory Deductions Engine (EPF, ESIC, Professional Tax, Gratuity)
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
        $gratuity = 0.0;
        $autoDeductions = [];

        $enablePf = $options['enable_pf'] ?? (!empty($employee->pf_number));
        $enableEsi = $options['enable_esi'] ?? (!empty($employee->esi_number) || ($grossEarnings > 0 && $grossEarnings <= 21000));
        $enablePt = $options['enable_pt'] ?? true;
        $enableGratuity = $options['enable_gratuity'] ?? true;

        // 1. EPF Calculation (12% employee, 8.33% EPS capped at 1250 + 3.67% EPF employer)
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

        // 4. Gratuity (Statutory liability: 15 days basic salary / 26 per year = 4.81% of basic per month)
        if ($enableGratuity && $basicSalary > 0) {
            $gratuity = round((15 / 26) * ($basicSalary / 12), 2);
        }

        return [
            'pf_employee' => $pfEmployee,
            'pf_employer' => $pfEmployer,
            'esi_employee' => $esiEmployee,
            'esi_employer' => $esiEmployer,
            'pt_amount' => $ptAmount,
            'gratuity' => $gratuity,
            'auto_deductions' => $autoDeductions,
        ];
    }

    /**
     * Automated Indian Income Tax / TDS Engine (FY 2024-25 / 2025-26 under Sec 115BAC & Old Regime)
     */
    public function computeAutomatedTds(Employee $employee, float $grossMonthly, int $month, int $year, string $regime = 'new', array $options = []): array
    {
        if (isset($options['tds_amount']) && is_numeric($options['tds_amount'])) {
            return [
                'monthly_tds' => (float)$options['tds_amount'],
                'annual_gross' => round($grossMonthly * 12, 2),
                'annual_tax' => round((float)$options['tds_amount'] * 12, 2),
                'taxable_income' => 0.0,
                'regime' => $regime,
            ];
        }

        // Indian Financial Year runs April to March
        // Determine remaining months in FY
        // If month is Apr (4), remaining = 12. If Mar (3), remaining = 1.
        $remainingMonths = ($month >= 4) ? (12 - ($month - 4)) : (3 - $month + 1);
        $remainingMonths = max(1, min(12, $remainingMonths));

        $annualGross = round($grossMonthly * 12, 2);
        $regime = strtolower($regime) === 'old' ? 'old' : 'new';

        $standardDeduction = ($regime === 'new') ? 75000.0 : 50000.0;
        $chapter6a = 0.0;

        if ($regime === 'old') {
            // Old Regime deductions: Section 80C (up to 1.5L), Section 80D (up to 25k)
            $declarations = !empty($employee->tax_declarations) ? json_decode($employee->tax_declarations, true) : [];
            $sec80c = min(150000.0, (float)($declarations['80c'] ?? 150000.0));
            $sec80d = min(25000.0, (float)($declarations['80d'] ?? 25000.0));
            $chapter6a = $sec80c + $sec80d;
        }

        $taxableIncome = max(0.0, $annualGross - $standardDeduction - $chapter6a);
        $annualTax = 0.0;

        if ($regime === 'new') {
            // New Regime Slabs (FY 2024-25 / 2025-26 Budget)
            // 0 - 3,00,000 : Nil
            // 3,00,001 - 7,00,000 : 5%
            // 7,00,001 - 10,00,000 : 10%
            // 10,00,001 - 12,00,000 : 15%
            // 12,00,001 - 15,00,000 : 20%
            // Above 15,00,000 : 30%
            if ($taxableIncome <= 700000.0) {
                // Section 87A rebate covers tax up to 7L
                $annualTax = 0.0;
            } else {
                $remIncome = $taxableIncome;
                if ($remIncome > 1500000.0) {
                    $annualTax += ($remIncome - 1500000.0) * 0.30;
                    $remIncome = 1500000.0;
                }
                if ($remIncome > 1200000.0) {
                    $annualTax += ($remIncome - 1200000.0) * 0.20;
                    $remIncome = 1200000.0;
                }
                if ($remIncome > 1000000.0) {
                    $annualTax += ($remIncome - 1000000.0) * 0.15;
                    $remIncome = 1000000.0;
                }
                if ($remIncome > 700000.0) {
                    $annualTax += ($remIncome - 700000.0) * 0.10;
                    $remIncome = 700000.0;
                }
                if ($remIncome > 300000.0) {
                    $annualTax += ($remIncome - 300000.0) * 0.05;
                }
            }
        } else {
            // Old Regime Slabs
            // 0 - 2,50,000 : Nil
            // 2,50,001 - 5,00,000 : 5%
            // 5,00,001 - 10,00,000 : 20%
            // Above 10,00,000 : 30%
            if ($taxableIncome <= 500000.0) {
                // Section 87A rebate covers tax up to 5L
                $annualTax = 0.0;
            } else {
                $remIncome = $taxableIncome;
                if ($remIncome > 1000000.0) {
                    $annualTax += ($remIncome - 1000000.0) * 0.30;
                    $remIncome = 1000000.0;
                }
                if ($remIncome > 500000.0) {
                    $annualTax += ($remIncome - 500000.0) * 0.20;
                    $remIncome = 500000.0;
                }
                if ($remIncome > 250000.0) {
                    $annualTax += ($remIncome - 250000.0) * 0.05;
                }
            }
        }

        // Add 4% Health and Education Cess
        if ($annualTax > 0) {
            $annualTax = round($annualTax * 1.04, 2);
        }

        $monthlyTds = $annualTax > 0 ? round($annualTax / 12, 2) : 0.0;

        return [
            'monthly_tds' => $monthlyTds,
            'annual_gross' => $annualGross,
            'annual_tax' => $annualTax,
            'taxable_income' => max(0.0, $annualGross - $standardDeduction - $chapter6a),
            'regime' => $regime,
        ];
    }

    /**
     * Sandwich Leave Rule Detector (Keka / Zoho Standard)
     * If an employee takes absent/unapproved leave before and after a weekend/holiday,
     * the intervening off days count as Loss of Pay.
     */
    protected function detectSandwichLeaveDays(Carbon $start, Carbon $end, array $weekOffs, Collection $holidays, array $attendanceByDate): int
    {
        $sandwichDays = 0;
        $period = CarbonPeriod::create($start, $end);
        $dates = iterator_to_array($period);
        $totalDays = count($dates);

        for ($i = 0; $i < $totalDays; $i++) {
            $currDate = $dates[$i];
            $currStr = $currDate->format('Y-m-d');
            $dayOfWeek = $currDate->dayOfWeek + 1;
            $isOff = in_array($dayOfWeek, $weekOffs) || $holidays->contains($currStr);

            if ($isOff) {
                // Find previous working day
                $prevAbsent = false;
                for ($p = $i - 1; $p >= 0; $p--) {
                    $pDate = $dates[$p];
                    $pStr = $pDate->format('Y-m-d');
                    $pDayOfWeek = $pDate->dayOfWeek + 1;
                    if (!in_array($pDayOfWeek, $weekOffs) && !$holidays->contains($pStr)) {
                        $pStatus = $attendanceByDate[$pStr] ?? 'absent';
                        $prevAbsent = in_array($pStatus, ['absent', 'leave']);
                        break;
                    }
                }

                // Find next working day
                $nextAbsent = false;
                for ($n = $i + 1; $n < $totalDays; $n++) {
                    $nDate = $dates[$n];
                    $nStr = $nDate->format('Y-m-d');
                    $nDayOfWeek = $nDate->dayOfWeek + 1;
                    if (!in_array($nDayOfWeek, $weekOffs) && !$holidays->contains($nStr)) {
                        $nStatus = $attendanceByDate[$nStr] ?? 'absent';
                        $nextAbsent = in_array($nStatus, ['absent', 'leave']);
                        break;
                    }
                }

                if ($prevAbsent && $nextAbsent) {
                    $sandwichDays++;
                }
            }
        }

        return $sandwichDays;
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
                'arrears' => $calc['arrears'] ?? 0.0,
                'bonus' => $calc['bonus'] ?? 0.0,
                'ctc' => $calc['ctc'] ?? 0.0,
                'gratuity' => $calc['gratuity'] ?? 0.0,
                'tax_regime' => $calc['tax_regime'] ?? 'new',
                'sandwich_deduction' => $calc['sandwich_deduction'] ?? 0.0,
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
            'sandwich_deduction' => 0.0,
            'other_deduction' => 0.0,
            'reimbursement' => 0.0,
            'arrears' => 0.0,
            'bonus' => 0.0,
            'ctc' => 0.0,
            'annual_ctc' => 0.0,
            'gratuity' => 0.0,
            'tax_regime' => 'new',
            'tax_details' => [],
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
