<?php

namespace App\Exports;

use App\Models\Attendance;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class AttendanceExport
{
    protected $startDate;
    protected $endDate;
    protected $companyId;
    protected $departmentId;
    protected $branchId;

    public function __construct($startDate, $endDate, $companyId, $departmentId = null, $branchId = null, $employee_id = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyId = $companyId;
        $this->departmentId = $departmentId;
        $this->branchId = $branchId;
        $this->employee_id = $employee_id; // Assuming this is not used in this export
    }

    public function export()
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // ✅ Build date range from request
        $dateRange = [];
        $tempDate = $start->copy();
        while ($tempDate->lte($end)) {
            $dateRange[] = $tempDate->format('d-m-Y');
            $tempDate->addDay();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Header row
        $header = [
            'Employee ID',
            'Employee Name',
            'Branch Name',
            'Department Name',
            'Company Name',
        ];

        foreach ($dateRange as $date) {
            $header[] = $date;
            $header[] = 'In Time';
            $header[] = 'Out Time';
            $header[] = 'Halfday';
        }

        $sheet->fromArray($header, null, 'A1');

        // Bold header
        $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($header));
        $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);

        $columnCount = count($header);

        // Loop through columns and set auto-size
        for ($col = 1; $col <= $columnCount; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $sheet->freezePane('C2');

        // ✅ Get employees
        $employees = \DB::table('employees')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('company_details', 'employees.company_id', '=', 'company_details.id')
            ->where('employees.company_id', $this->companyId)
            ->when($this->departmentId, fn($query) => $query->where('employees.department_id', $this->departmentId))
            ->when($this->branchId, fn($query) => $query->where('employees.branch_id', $this->branchId))
            ->when($this->employee_id, fn($query) => $query->where('employees.id', $this->employee_id))
            ->select(
                'employees.id',
                'employees.name',
                'branches.branch_name as branch_name',
                'departments.name as department_name',
                'company_details.company_name as company_name'
            )
            ->get();

        $rowIndex = 2;

        foreach ($employees as $employee) {
            // ✅ Attendance for this employee within date range
            $attendances = \DB::table('attendances')
                ->where('employee_id', $employee->id)
                ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->get()
                ->keyBy(fn($a) => Carbon::parse($a->date)->format('d-m-Y'));

   
            

            $row = [
                $employee->id,
                $employee->name,
                $employee->branch_name,
                $employee->department_name,
                $employee->company_name,
            ];

            foreach ($dateRange as $date) {
                $a = $attendances->get($date);
                $row[] = $a->attendance ?? '';
                $row[] = $a->in_time ?? '';
                $row[] = $a->out_time ?? '';
                $row[] = isset($a->halfday) && ($a->halfday==1) ?'Yes': '';
            }

            $sheet->fromArray($row, null, 'A' . $rowIndex++);
        }

        // ✅ Export file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'attendance_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path("app/public/{$fileName}");

        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}