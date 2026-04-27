<?php

namespace App\Exports;

use App\Models\Employee;
use Barryvdh\DomPDF\PDF;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalarySummary;

class SalaryPDFExport
{
    protected $company;
    protected $emp_id;
    protected $month;
    protected $year;

    public function __construct($company, $emp_id, $month, $year)
    {
        $this->company = $company;
        $this->emp_id = $emp_id;
        $this->month = $month;
        $this->year = $year;
    }

    public function export()
    {
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        
        // Get salary summary
        $salarySummary = EmployeeSalarySummary::where('company_id', $this->company->id)
            ->where('salary_month', $this->month)
            ->where('salary_year', $this->year)
            ->where('employee_id', $this->emp_id)
            ->first();
        
        // Get salary components
        $employeeSalaries = EmployeeSalary::where('company_id', $this->company->id)
            ->where('salary_month', $this->month)
            ->where('salary_year', $this->year)
            ->where('employee_id', $this->emp_id)
            ->get();

        $employee = Employee::with(['department', 'city', 'bankAccount'])->findOrFail($this->emp_id);

        $data = [
            'monthArray' => $monthArray,
            'salaryMonth' => $this->month,
            'salaryYear' => $this->year,
            'employeeSalaries' => $employeeSalaries,
            'company' => $this->company,
            'employee' => $employee,
            'salarySummary' => $salarySummary,
        ];

        $pdf = app(PDF::class)->loadView('user.salarytype.salary_pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
        ]);
        
        $filename = 'salary_' . $this->month . '_' . $this->year . '_' . $employee->emp_id . '.pdf';

        return $pdf->download($filename);
    }
}