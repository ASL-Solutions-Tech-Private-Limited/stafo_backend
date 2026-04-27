<?php

namespace App\Exports;

use App\Models\EmployeeLeave;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;

class LeavePDFExport
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
        $leaves = EmployeeLeave::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('company_id', $this->companyId)
            ->when($this->departmentId, fn($q) => $q->where('department_id', $this->departmentId))
            ->when($this->branchId, fn($q) => $q->where('branch_id', $this->branchId))
            ->with(['employee.company', 'branch', 'department'])
            ->get();

        // dd($leaves);
        $companyName = optional($leaves->first()?->employee?->company)->company_name ?? 'N/A';

        $data = [
            'leaves' => $leaves,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'companyName' => $companyName,
        ];

        $pdf = app(PDF::class)->loadView('user.report.pdf_leave', $data);

        $filename = 'leave_report_' . Carbon::parse($this->startDate)->format('Ymd') . '_to_' . Carbon::parse($this->endDate)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}