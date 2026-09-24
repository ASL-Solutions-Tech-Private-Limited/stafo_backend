<?php

namespace App\Exports;

use App\Models\CompanyDetail;
use App\Models\Employee;
use Barryvdh\DomPDF\PDF;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalarySummary;
use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Carbon\Carbon;
use App\Helpers\Helper;

class SalaryPDFExport
{
    protected $company;
    protected $emp_id;
    protected $month;
    protected $year;

    public function __construct($company, $emp_id, $month, $year)
    {
        if ($company instanceof CompanyDetail) {
            $this->company = $company;
        } elseif (is_numeric($company)) {
            $this->company = CompanyDetail::find($company);
        } else {
            $this->company = $company;
        }

        $this->emp_id = $emp_id;
        $this->month = (int)$month;
        $this->year = (int)$year;
    }

    public function download($customFilename = null)
    {
        $pdf = $this->getPdf();
        
        $employee = Employee::find($this->emp_id);
        $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthName = $monthArray[$this->month - 1] ?? $this->month;
        $cleanEmpName = $employee ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $employee->name) : 'Employee';
        $filename = $customFilename ?: ('SalarySlip_' . $cleanEmpName . '_' . $monthName . '_' . $this->year . '.pdf');

        return $pdf->download($filename);
    }

    public function export($customFilename = null)
    {
        return $this->download($customFilename);
    }

    public function getPdf()
    {
        $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $monthName = $monthArray[$this->month - 1] ?? $this->month;
        $companyId = $this->company ? $this->company->id : null;

        $employee = Employee::with(['department', 'designation', 'bankAccount', 'company', 'branch'])->findOrFail($this->emp_id);
        if (!$this->company && $employee->company) {
            $this->company = $employee->company;
            $companyId = $this->company->id;
        }

        // Get salary summary
        $salarySummary = EmployeeSalarySummary::where('employee_id', $this->emp_id)
            ->where('salary_month', $this->month)
            ->where('salary_year', $this->year)
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->latest()
            ->first();

        // Get salary components
        $employeeSalaries = EmployeeSalary::with('salarytype')
            ->where('employee_id', $this->emp_id)
            ->where('salary_month', $this->month)
            ->where('salary_year', $this->year)
            ->when($companyId, function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->get();

        // Company Logo in Base64 for guaranteed DomPDF rendering
        $logoBase64 = null;
        $logoCandidates = [];
        if ($this->company && !empty($this->company->image_name)) {
            $logoCandidates[] = public_path('uploads/company_logo/' . $this->company->image_name);
            $logoCandidates[] = public_path('uploads/compnay_logo/' . $this->company->image_name);
            $logoCandidates[] = public_path('uploads/' . $this->company->image_name);
        }
        $logoCandidates[] = public_path('assets/images/payslip_default_logo.png');
        $logoCandidates[] = public_path('assets/images/logo.png');

        foreach ($logoCandidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : ($ext === 'svg' ? 'image/svg+xml' : 'image/png');
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($candidate));
                break;
            }
        }

        // Dynamic Company Registered Address (NO hardcoded fallbacks)
        $regAddress = '';
        if ($this->company) {
            $addrParts = [];
            if (!empty($this->company->address)) {
                $addrParts[] = trim($this->company->address);
            }
            
            // Resolve City
            $cityName = '';
            if (!empty($this->company->city)) {
                $cityName = is_numeric($this->company->city) ? (City::find($this->company->city)?->name ?? '') : $this->company->city;
            }
            if ($cityName && !empty($this->company->address) && stripos($this->company->address, $cityName) === false) {
                $addrParts[] = $cityName;
            }

            // Resolve State
            $stateName = '';
            if (!empty($this->company->state)) {
                $stateName = is_numeric($this->company->state) ? (State::find($this->company->state)?->name ?? '') : $this->company->state;
            }
            if ($stateName && !empty($this->company->address) && stripos($this->company->address, $stateName) === false) {
                $addrParts[] = $stateName;
            }

            // Resolve Country
            $countryName = '';
            if (!empty($this->company->country)) {
                $countryName = is_numeric($this->company->country) ? (Country::find($this->company->country)?->name ?? '') : $this->company->country;
            }
            if ($countryName && !empty($this->company->address) && stripos($this->company->address, $countryName) === false) {
                $addrParts[] = $countryName;
            }

            $regAddress = implode(', ', array_filter($addrParts));

            // Pin code (only append if not already present in the string)
            if (!empty($this->company->pin) && stripos($regAddress, (string)$this->company->pin) === false) {
                $regAddress .= ' - ' . $this->company->pin;
            }
        }

        // Dynamic Branch Address & Branch Name (NO hardcoded fallbacks)
        $branchAddress = '';
        $branchName = '';
        $branch = null;
        if ($employee->branch) {
            $branch = $employee->branch;
        } elseif ($this->company) {
            $branch = Branch::where('company_id', $this->company->id)->first();
        }

        if ($branch) {
            $branchName = $branch->branch_name ?? '';
            $branchAddress = $branch->branch_address ?? '';
        }

        // Pay Period & Dates
        $payPeriod = $monthName . ' ' . $this->year;
        $payDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth()->format('d F Y');

        // Employee Info (Dynamic, no hardcoded 'Mr.' or 'Tech (IT Department)')
        $employeeName = $employee->name ?? 'N/A';
        $employeeId = $employee->emp_id ?: ('EMP-' . str_pad($employee->id, 5, '0', STR_PAD_LEFT));
        $doj = !empty($employee->date_of_joining) ? Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A';
        $designationName = $employee->designation->name ?? ($employee->position ?? 'N/A');
        $departmentName = $employee->department->name ?? ($salarySummary->department_name ?? 'N/A');
        $workingDays = $salarySummary->working_days ?? ($salarySummary->total_working_days ?? Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth);
        $paidDays = isset($salarySummary->present_days) ? $salarySummary->present_days : max(0, $workingDays - ($salarySummary->absent_days ?? 0));
        $absentDays = $salarySummary->absent_days ?? 0;
        
        // Bank Details
        $bankName = $employee->bankAccount->bank_name ?? 'N/A';
        $bankAccount = $employee->bankAccount->account_number ?? ($employee->account_number ?? 'N/A');
        $ifscCode = $employee->bankAccount->ifsc_code ?? 'N/A';

        // Statutory Numbers (PF / ESI / UAN / PAN)
        $pfNumber = $employee->pf_number ?? null;
        $esiNumber = $employee->esi_number ?? null;
        $uanNumber = $employee->uan ?? null;
        $panNumber = $employee->pan ?? null;

        // Dynamic Earnings Calculation
        $basicSalary = (float)($salarySummary->basic_salary ?? ($employee->salary ?? 0));
        $earningsList = [];
        $earningsList[] = [
            'name' => 'Basic Salary',
            'amount' => $basicSalary,
        ];
        $totalEarnings = $basicSalary;

        // Dynamic components from employeeSalaries
        if ($employeeSalaries->isNotEmpty()) {
            foreach ($employeeSalaries as $salary) {
                $ptype = $salary->payment_type ?: ($salary->salarytype->payment_type ?? '');
                $amt = (float)$salary->amount;
                $label = trim($salary->label ?: ($salary->salarytype->salary_type ?? ''));

                if ($amt <= 0 || strcasecmp($label, 'Basic Salary') === 0) {
                    continue;
                }

                if ($ptype === 'Earning' || (empty($ptype) && stripos($label, 'deduct') === false && stripos($label, 'pf') === false && stripos($label, 'tax') === false && stripos($label, 'esi') === false && stripos($label, 'pt') === false)) {
                    $earningsList[] = [
                        'name' => $label ?: 'Allowance',
                        'amount' => $amt,
                    ];
                    $totalEarnings += $amt;
                }
            }
        }

        // Add Reimbursement if any
        $reimbursement = (float)($salarySummary->reimbursement ?? 0);
        if ($reimbursement > 0) {
            $earningsList[] = [
                'name' => 'Expense Reimbursement',
                'amount' => $reimbursement,
            ];
            $totalEarnings += $reimbursement;
        }

        // Arrears & Bonus
        $existingPdfEarnNames = array_map(fn($e) => strtolower($e['name']), $earningsList);
        if (($salarySummary->arrears ?? 0) > 0 && !array_filter($existingPdfEarnNames, fn($n) => str_contains($n, 'arrear'))) {
            $earningsList[] = ['name' => 'Salary Arrears', 'amount' => (float)$salarySummary->arrears];
            $totalEarnings += (float)$salarySummary->arrears;
        }
        if (($salarySummary->bonus ?? 0) > 0 && !array_filter($existingPdfEarnNames, fn($n) => str_contains($n, 'bonus'))) {
            $earningsList[] = ['name' => 'Performance Bonus', 'amount' => (float)$salarySummary->bonus];
            $totalEarnings += (float)$salarySummary->bonus;
        }

        $grossSalary = (float)($salarySummary->gross_salary ?? $totalEarnings);

        // Dynamic Deductions Calculation
        $deductionsList = [];
        $totalDeductions = 0;

        if ($employeeSalaries->isNotEmpty()) {
            foreach ($employeeSalaries as $salary) {
                $ptype = $salary->payment_type ?: ($salary->salarytype->payment_type ?? '');
                $amt = (float)$salary->amount;
                $label = trim($salary->label ?: ($salary->salarytype->salary_type ?? ''));

                if ($amt <= 0 || strcasecmp($label, 'Basic Salary') === 0) {
                    continue;
                }

                if ($ptype === 'Deduction' || stripos($label, 'deduct') !== false || stripos($label, 'pf') !== false || stripos($label, 'tax') !== false || stripos($label, 'esi') !== false || stripos($label, 'pt') !== false) {
                    $deductionsList[] = [
                        'name' => $label ?: 'Deduction',
                        'amount' => $amt,
                    ];
                    $totalDeductions += $amt;
                }
            }
        }

        // Statutory deductions fallback from summary if not already in list
        $existingPdfDedNames = array_map(fn($d) => strtolower($d['name']), $deductionsList);
        if (($salarySummary->pf_employee ?? 0) > 0 && !array_filter($existingPdfDedNames, fn($n) => str_contains($n, 'provident') || str_contains($n, 'pf'))) {
            $deductionsList[] = ['name' => 'Provident Fund (PF - 12%)', 'amount' => (float)$salarySummary->pf_employee];
            $totalDeductions += (float)$salarySummary->pf_employee;
        }
        if (($salarySummary->esi_employee ?? 0) > 0 && !array_filter($existingPdfDedNames, fn($n) => str_contains($n, 'esi'))) {
            $deductionsList[] = ['name' => 'ESIC (0.75%)', 'amount' => (float)$salarySummary->esi_employee];
            $totalDeductions += (float)$salarySummary->esi_employee;
        }
        if (($salarySummary->pt_amount ?? 0) > 0 && !array_filter($existingPdfDedNames, fn($n) => str_contains($n, 'professional') || str_contains($n, 'pt'))) {
            $deductionsList[] = ['name' => 'Professional Tax (PT)', 'amount' => (float)$salarySummary->pt_amount];
            $totalDeductions += (float)$salarySummary->pt_amount;
        }
        if (($salarySummary->tds_amount ?? 0) > 0 && !array_filter($existingPdfDedNames, fn($n) => str_contains($n, 'tds') || str_contains($n, 'tax'))) {
            $deductionsList[] = ['name' => 'TDS / Income Tax (' . strtoupper($salarySummary->tax_regime ?? 'New') . ' Regime)', 'amount' => (float)$salarySummary->tds_amount];
            $totalDeductions += (float)$salarySummary->tds_amount;
        }

        // Dynamic Other Deductions (e.g. Loss of Pay / LOP / absent deduction)
        $otherDeduction = (float)($salarySummary->other_deduction ?? 0);
        if ($otherDeduction > 0) {
            $otherLabel = 'Attendance Loss of Pay (LOP)';
            $deductionsList[] = [
                'name' => $otherLabel,
                'amount' => $otherDeduction,
            ];
            $totalDeductions += $otherDeduction;
        }

        // Net Salary
        $netSalary = (float)($salarySummary->net_salary ?? max(0, $grossSalary - $totalDeductions));

        // Amount in Words (Indian Standard)
        $netSalaryInWords = self::numberToWords($netSalary);

        $data = [
            'company' => $this->company,
            'logoBase64' => $logoBase64,
            'regAddress' => $regAddress,
            'branchName' => $branchName,
            'branchAddress' => $branchAddress,
            'payPeriod' => $payPeriod,
            'payDate' => $payDate,
            'monthName' => $monthName,
            'salaryMonth' => $this->month,
            'salaryYear' => $this->year,
            'employee' => $employee,
            'employeeName' => $employeeName,
            'employeeId' => $employeeId,
            'salarySummary' => $salarySummary,
            'doj' => $doj,
            'designationName' => $designationName,
            'departmentName' => $departmentName,
            'workingDays' => $workingDays,
            'paidDays' => $paidDays,
            'absentDays' => $absentDays,
            'bankName' => $bankName,
            'bankAccount' => $bankAccount,
            'ifscCode' => $ifscCode,
            'pfNumber' => $pfNumber,
            'esiNumber' => $esiNumber,
            'uanNumber' => $uanNumber,
            'panNumber' => $panNumber,
            'earningsList' => $earningsList,
            'grossSalary' => $grossSalary,
            'deductionsList' => $deductionsList,
            'totalDeductions' => $totalDeductions,
            'netSalary' => $netSalary,
            'netSalaryInWords' => $netSalaryInWords,
        ];

        $pdf = app(PDF::class)->loadView('user.salarytype.salary_pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
        ]);

        return $pdf;
    }

    public static function numberToWords($amount)
    {
        $amount = round((float)$amount, 2);
        $rupees = (int)floor($amount);
        $paise = (int)round(($amount - $rupees) * 100);

        $rupeesWord = Helper::convert($rupees);
        if (!$rupeesWord) {
            $rupeesWord = 'Zero';
        }
        $result = 'Rupees ' . ucwords(str_replace(['-', ','], [' ', ''], $rupeesWord));

        if ($paise > 0) {
            $paiseWord = Helper::convert($paise);
            $result .= ' and ' . ucwords(str_replace(['-', ','], [' ', ''], $paiseWord)) . ' Paise';
        }

        return $result . ' Only';
    }
}