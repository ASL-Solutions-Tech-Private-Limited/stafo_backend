<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\PDF;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use League\Csv\Writer;
use Carbon\Carbon;

class EmployeeReportController extends Controller
{
    public function exportEmployee(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;
        $companyId = $request->company_id;

        if (!$startDate || !$endDate || !$companyId) {
            return response()->json(['error' => 'Missing required parameters.'], 200);
        }

        if ($format === 'excel') {
            return $this->exportExcel($startDate, $endDate, $companyId, $departmentId, $branchId);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($startDate, $endDate, $companyId, $departmentId, $branchId);
        } elseif ($format === 'csv') {
            return $this->exportCsv($startDate, $endDate, $companyId, $departmentId, $branchId);
        } else {
            return response()->json(['error' => 'Invalid format selected'], 400);
        }
    }

    private function getFilteredEmployees($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        return Employee::whereBetween('created_at', [$startDate, $endDate])
            ->where('company_id', $companyId)
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['branch', 'department'])
            ->get(['emp_id', 'name', 'email', 'phone', 'position', 'salary', 'created_at', 'branch_id', 'department_id', 'company_id']);
    }

    private function exportExcel($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $employees = $this->getFilteredEmployees($startDate, $endDate, $companyId, $departmentId, $branchId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


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
        ];


        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column, $header);
        }

        $row = 2;
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $row, $employee->emp_id);
            $sheet->setCellValue('B' . $row, $employee->name);
            $sheet->setCellValue('C' . $row, $employee->email);
            $sheet->setCellValue('D' . $row, $employee->phone);
            $sheet->setCellValue('E' . $row, $employee->position);
            $sheet->setCellValue('F' . $row, $employee->salary);
            $sheet->setCellValue('G' . $row, Carbon::parse($employee->created_at)->format('Y-m-d'));
            $sheet->setCellValue('H' . $row, $employee->branch->branch_name ?? 'N/A');
            $sheet->setCellValue('I' . $row, $employee->department->name ?? 'N/A');
            $row++;
        }

        $fileName = 'employee_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        $filePath = public_path('uploads/excel/' . $fileName);

        if (!File::exists(public_path('uploads/excel'))) {
            File::makeDirectory(public_path('uploads/excel'), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Employee Excel file generated.',
            'download_url' => url('uploads/excel/' . $fileName)
        ]);
    }

    private function exportPdf($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $employees = $this->getFilteredEmployees($startDate, $endDate, $companyId, $departmentId, $branchId);

        $employeeData = $employees->map(function ($e) {
            return [
                'emp_id' => $e->emp_id,
                'name' => $e->name,
                'email' => $e->email,
                'phone' => $e->phone,
                'position' => $e->position,
                'salary' => $e->salary,
                'created_at' => $e->created_at->format('Y-m-d'),
                'branch_name' => $e->branch->branch_name ?? 'N/A',
                'department_name' => $e->department->name ?? 'N/A',
                'company_name'   => optional($e->company)->company_name ?? 'N/A',
            ];
        });

        //dd($employeeData);

        $data = [
            'employees' => $employeeData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        $pdf = app(PDF::class)->loadView('user.report.pdf_employee', $data);

        $fileName = 'employee_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        $filePath = public_path('uploads/pdf/' . $fileName);

        if (!File::exists(public_path('uploads/pdf'))) {
            File::makeDirectory(public_path('uploads/pdf'), 0777, true);
        }

        $pdf->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Employee PDF file generated.',
            'download_url' => url('uploads/pdf/' . $fileName)
        ]);
    }

    private function exportCsv($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $employees = $this->getFilteredEmployees($startDate, $endDate, $companyId, $departmentId, $branchId);

        $fileName = 'employee_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.csv';
        $filePath = public_path('uploads/csv/' . $fileName);

        if (!File::exists(public_path('uploads/csv'))) {
            File::makeDirectory(public_path('uploads/csv'), 0777, true);
        }

        $csv = Writer::createFromPath($filePath, 'w+');
        $csv->insertOne(['Employee ID', 'Name', 'Email', 'Phone', 'Position', 'Salary', 'Date of Joining', 'Branch', 'Department']);

        foreach ($employees as $e) {
            $csv->insertOne([
                $e->emp_id,
                $e->name,
                $e->email,
                $e->phone,
                $e->position,
                $e->salary,
                Carbon::parse($e->created_at)->format('Y-m-d'),
                $e->branch->branch_name ?? 'N/A',
                $e->department->name ?? 'N/A'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee CSV file generated.',
            'download_url' => url('uploads/csv/' . $fileName)
        ]);
    }



    public function getAllEmployeeReports(Request $request)
    {
        $companyId = $request->company_id;

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing company_id parameter.'
            ], 201);
        }

        $employeeDates = Employee::where('company_id', $companyId)
            ->pluck('created_at')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Ymd');
            })
            ->toArray();

        if (empty($employeeDates)) {
            return response()->json([
                'success' => true,
                'message' => 'No employee data found for this company.',
                'data'    => []
            ], 200);
        }

        $reportDirectories = [
            'excel' => public_path('uploads/excel'),
            'pdf'   => public_path('uploads/pdf'),
            'csv'   => public_path('uploads/csv'),
        ];

        $reportList = [];

        foreach ($reportDirectories as $type => $path) {
            if (!File::exists($path)) continue;

            $files = File::files($path);
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                if (str_starts_with($fileName, 'employee_report_')) {
                    foreach ($employeeDates as $date) {
                        $modifiedTime = File::lastModified($file);

                        $reportList[] = [
                            'file_name'    => 'employee_report',
                            'download_url' => url("uploads/$type/" . $fileName),
                            'file_type'    => strtoupper($type),
                            'created_at'   => Carbon::createFromTimestamp(File::lastModified($file))->toDateTimeString(),
                            'exported_date' => Carbon::createFromTimestamp($modifiedTime)->toDateString(),

                        ];
                        break; // avoid duplicate

                    }
                }
            }
        }

        usort($reportList, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return response()->json([
            'success' => true,
            'message' => 'Employee reports generated for this company.',
            'data'    => $reportList
        ], 200);
    }
}