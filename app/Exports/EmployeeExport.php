<?php

namespace App\Exports;

use App\Models\Employee;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class EmployeeExport
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
        $employees = Employee::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('company_id', $this->companyId)
            ->when($this->departmentId, function ($query) {
                return $query->where('department_id', $this->departmentId);
            })
            ->when($this->branchId, function ($query) {
                return $query->where('branch_id', $this->branchId);
            })
            ->get(['emp_id', 'name', 'email', 'phone', 'position', 'salary', 'created_at', 'branch_id', 'department_id', 'company_id']);


        // dd($employees);

        // Debugging: Check if employees are being fetched correctly
        // dd($employees);

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the sheet
        $headers = [
            'A1' => 'Employee ID',
            'B1' => 'Name',
            'C1' => 'Email',
            'D1' => 'Phone',
            'E1' => 'Position',
            'F1' => 'Salary',
            'G1' => 'Date of Joining',
            'H1' => 'Branch',
            'I1' => 'Department',
            'J1' => 'Company',

        ];

        // Loop through the headers and add them to the sheet
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column, $header);
        }

        // Write employee data to the sheet
        $row = 2; // Start from row 2 because row 1 contains the headers
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $row, $employee->emp_id);
            $sheet->setCellValue('B' . $row, $employee->name);
            $sheet->setCellValue('C' . $row, $employee->email);
            $sheet->setCellValue('D' . $row, $employee->phone);
            $sheet->setCellValue('E' . $row, $employee->position);
            $sheet->setCellValue('F' . $row, $employee->salary);
            $sheet->setCellValue('G' . $row, $employee->created_at->format('Y-m-d'));  // Formatting the date
            $sheet->setCellValue('H' . $row, $employee->branch ? $employee->branch->branch_name : 'N/A');
            $sheet->setCellValue('I' . $row, $employee->department ? $employee->department->name : 'N/A');
            $sheet->setCellValue('J' . $row, optional($employee->company)->company_name ?? 'N/A');

            $row++;
        }

        // Create a writer instance and write the Excel file to a PHP output stream
        $writer = new Xlsx($spreadsheet);

        // Set the filename for the download
        // $filename = 'employee_report_' . $this->year . '_' . $this->month . '.xlsx';
        $filename = 'employee_report_' . now()->format('Ymd_His') . '.xlsx';


        // Return the Excel file as a download response
        return Response::stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Cache-Control' => 'max-age=1',
            ]
        );
    }
}