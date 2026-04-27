<?php

namespace App\Exports;

use App\Models\EmployeeLeave;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class LeaveExport
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
            ->when($this->departmentId, fn($query) => $query->where('department_id', $this->departmentId))
            ->when($this->branchId, fn($query) => $query->where('branch_id', $this->branchId))
            ->with(['employee.company', 'branch', 'department']) // eager load
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Employee ID',
            'B1' => 'Employee Name',
            'C1' => 'Leave Type',
            'D1' => 'From Date',
            'E1' => 'To Date',
            'F1' => 'Reason',
            'G1' => 'Days',
            'H1' => 'Status',
            'I1' => 'Branch Name',
            'J1' => 'Department Name',
            'K1' => 'Company Name'
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col, $title);
        }

        // Data Rows
        $row = 2;
        foreach ($leaves as $leave) {
            $sheet->setCellValue('A' . $row, $leave->employee_id);
            $sheet->setCellValue('B' . $row, $leave->employee->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $leave->leave_type);
            $sheet->setCellValue('D' . $row, Carbon::parse($leave->from_date)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, Carbon::parse($leave->to_date)->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $leave->reason);
            $sheet->setCellValue('G' . $row, $leave->days);
            $sheet->setCellValue('H' . $row, $leave->status);
            $sheet->setCellValue('I' . $row, $leave->branch->branch_name ?? 'N/A');
            $sheet->setCellValue('J' . $row, $leave->department->name ?? 'N/A');
            $sheet->setCellValue('K' . $row, $leave->employee->company->company_name ?? 'N/A');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'leave_report_' . Carbon::parse($this->startDate)->format('Ymd') . '_to_' . Carbon::parse($this->endDate)->format('Ymd') . '.xlsx';

        return Response::stream(
            fn() => $writer->save('php://output'),
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}