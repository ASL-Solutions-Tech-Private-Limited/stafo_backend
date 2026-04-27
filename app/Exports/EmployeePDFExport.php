<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use Barryvdh\DomPDF\PDF;

class EmployeePDFExport
{
    protected $startDate;
    protected $endDate;
    protected $companyId;
    protected $departmentId;
    protected $branchId;

    public function __construct($startDate, $endDate, $companyId, $departmentId = null, $branchId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyId = $companyId;
        $this->departmentId = $departmentId;
        $this->branchId = $branchId;
    }

    public function export()
    {
        $employees = Employee::with(['branch', 'department', 'company'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('company_id', $this->companyId)
            ->when($this->departmentId, function ($query) {
                return $query->where('department_id', $this->departmentId);
            })
            ->when($this->branchId, function ($query) {
                return $query->where('branch_id', $this->branchId);
            })
            ->get([
                'emp_id',
                'name',
                'email',
                'phone',
                'position',
                'salary',
                'created_at',
                'branch_id',
                'department_id',
                'company_id'
            ]);

        $employeeData = $employees->map(function ($employee) {
            return [
                'emp_id'         => $employee->emp_id,
                'name'           => $employee->name,
                'email'          => $employee->email,
                'phone'          => $employee->phone,
                'position'       => $employee->position,
                'salary'         => $employee->salary,
                'created_at'     => $employee->created_at->format('Y-m-d'),
                'branch_name'    => optional($employee->branch)->branch_name ?? 'N/A',
                'department_name' => optional($employee->department)->name ?? 'N/A',
                'company_name'   => optional($employee->company)->company_name ?? 'N/A',
            ];
        });

        $data = [
            'employees'  => $employeeData,
            'startDate'  => $this->startDate,
            'endDate'    => $this->endDate,
        ];

        // Generate the PDF and return download
        $pdf = app(PDF::class)->loadView('user.report.pdf_employee', $data)->setPaper('a4', 'landscape');
        return $pdf->download('employee_report_' . now()->format('Ymd_His') . '.pdf');
    }
}