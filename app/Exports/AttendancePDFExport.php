<?php

namespace App\Exports;

use App\Models\Attendance;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;

class AttendancePDFExport
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
        $attendances = Attendance::whereBetween('date', [$this->startDate, $this->endDate])
            ->where('company_id', $this->companyId)
            ->when($this->departmentId, fn($query) => $query->where('department_id', $this->departmentId))
            ->when($this->branchId, fn($query) => $query->where('branch_id', $this->branchId))
            ->with(['employee.company', 'branch', 'department'])
            ->get();

        $data = [
            'attendances' => $attendances,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $pdf = app(PDF::class)->loadView('user.report.pdf_attendance', $data)->setPaper('a4', 'landscape');
        $fileName = 'attendance_report_' . Carbon::parse($this->startDate)->format('Ymd') . '_to_' . Carbon::parse($this->endDate)->format('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}