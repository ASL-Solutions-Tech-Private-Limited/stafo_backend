<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeLeave;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\CompanyDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\File;
use League\Csv\Writer;

class LeaveReportController extends Controller
{
    public function exportLeave(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;
        $companyId = $request->company_id;

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

    private function getFilteredLeaves($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        return EmployeeLeave::whereBetween('created_at', [$startDate, $endDate])
            ->where('company_id', $companyId)
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();
    }

    private function exportExcel($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $leaves = $this->getFilteredLeaves($startDate, $endDate, $companyId, $departmentId, $branchId);
        $companyName = CompanyDetail::find($companyId)->company_name ?? 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header: Company and Date Range
        $sheet->setCellValue('A1', "Company: $companyName");
        $sheet->setCellValue('A2', "Report Period: $startDate to $endDate");
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');

        // Table Headers
        $headers = [
            'A3' => 'Employee ID',
            'B3' => 'Employee Name',
            'C3' => 'Leave Type',
            'D3' => 'From Date',
            'E3' => 'To Date',
            'F3' => 'Reason',
            'G3' => 'Days',
            'H3' => 'Status',
            'I3' => 'Branch Name',
            'J3' => 'Department Name',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $row = 4;
        foreach ($leaves as $leave) {
            $employee = Employee::find($leave->employee_id);
            $branch = Branch::find($leave->branch_id);
            $department = Department::find($leave->department_id);

            $sheet->setCellValue('A' . $row, $leave->employee_id);
            $sheet->setCellValue('B' . $row, $employee->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $leave->leave_type);
            $sheet->setCellValue('D' . $row, Carbon::parse($leave->from_date)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, Carbon::parse($leave->to_date)->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $leave->reason);
            $sheet->setCellValue('G' . $row, $leave->days);
            $sheet->setCellValue('H' . $row, $leave->status);
            $sheet->setCellValue('I' . $row, $branch->branch_name ?? 'N/A');
            $sheet->setCellValue('J' . $row, $department->name ?? 'N/A');
            $row++;
        }

        $fileName = "employee_leave_{$startDate}_to_{$endDate}.xlsx";
        $filePath = public_path('uploads/excel/' . $fileName);
        if (!File::exists(public_path('uploads/excel'))) {
            File::makeDirectory(public_path('uploads/excel'), 0777, true);
        }

        (new Xlsx($spreadsheet))->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Employee Leave Excel file generated.',
            'download_url' => url('uploads/excel/' . $fileName),
        ]);
    }

    private function exportPdf($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $leaves = $this->getFilteredLeaves($startDate, $endDate, $companyId, $departmentId, $branchId);
        $companyName = CompanyDetail::find($companyId)->company_name ?? 'N/A';

        $data = [
            'leaves' => $leaves,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'companyName' => $companyName
        ];

        $pdf = app(PDF::class)->loadView('user.report.pdf_leave', $data);

        $fileName = "employee_leave_{$startDate}_to_{$endDate}.pdf";
        $filePath = public_path('uploads/pdf/' . $fileName);
        if (!File::exists(public_path('uploads/pdf'))) {
            File::makeDirectory(public_path('uploads/pdf'), 0777, true);
        }

        $pdf->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Employee Leave PDF file generated.',
            'download_url' => url('uploads/pdf/' . $fileName),
        ]);
    }

    private function exportCsv($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $leaves = $this->getFilteredLeaves($startDate, $endDate, $companyId, $departmentId, $branchId);
        $companyName = CompanyDetail::find($companyId)->company_name ?? 'N/A';

        $folderPath = public_path('uploads/csv');
        $fileName = "employee_leave_{$startDate}_to_{$endDate}.csv";
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        $csv = Writer::createFromPath($filePath, 'w+');
        $csv->insertOne(["Company: $companyName"]);
        $csv->insertOne(["Report Period: $startDate to $endDate"]);
        $csv->insertOne([]);

        $csv->insertOne([
            'Employee ID',
            'Employee Name',
            'Leave Type',
            'From Date',
            'To Date',
            'Reason',
            'Days',
            'Status',
            'Branch Name',
            'Department Name'
        ]);

        foreach ($leaves as $leave) {
            $employee = Employee::find($leave->employee_id);
            $branch = Branch::find($leave->branch_id);
            $department = Department::find($leave->department_id);

            $csv->insertOne([
                $leave->employee_id,
                $employee->name ?? 'N/A',
                $leave->leave_type,
                Carbon::parse($leave->from_date)->format('Y-m-d'),
                Carbon::parse($leave->to_date)->format('Y-m-d'),
                $leave->reason,
                $leave->days,
                $leave->status,
                $branch->branch_name ?? 'N/A',
                $department->name ?? 'N/A'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee Leave CSV file generated.',
            'download_url' => url('uploads/csv/' . $fileName),
        ]);
    }

    public function getAllLeaveReports(Request $request)
    {
        $companyId = $request->company_id;

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing company_id parameter.'
            ], 201);
        }

        $leaveDates = EmployeeLeave::where('company_id', $companyId)
            ->pluck('created_at')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Ymd');
            })
            ->unique()
            ->toArray();

        // sdd($leaveDates);

        if (empty($leaveDates)) {
            return response()->json([
                'success' => true,
                'message' => 'No leave data found for this company.',
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



                if (str_starts_with($fileName, 'employee_leave_')) {
                    foreach ($leaveDates as $date) {
                        $modifiedTime = File::lastModified($file);
                        $reportList[] = [
                            'file_name'    => "leave_report",
                            'download_url' => url("uploads/$type/" . $fileName),
                            'file_type'    => strtoupper($type),
                            'created_at'   => Carbon::createFromTimestamp(File::lastModified($file))->toDateTimeString(),
                            'exported_date' => Carbon::createFromTimestamp($modifiedTime)->toDateString(),
                        ];
                        break;
                    }
                }
            }
        }

        // Sort latest first
        usort($reportList, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return response()->json([
            'success' => true,
            'message' => 'Leave reports generated for this company.',
            'data'    => $reportList
        ], 200);
    }
}