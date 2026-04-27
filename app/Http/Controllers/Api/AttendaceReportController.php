<?php

namespace App\Http\Controllers\api;

use Carbon\Carbon;
use League\Csv\Writer;
use App\Models\Company;
use Barryvdh\DomPDF\PDF;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendaceReportController extends Controller
{
    public function exportAttendance(Request $request)
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

    private function getFilteredAttendances($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        return Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('company_id', $companyId)
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['employee', 'branch', 'department'])
            ->get([
                'employee_id',
                'branch_id',
                'department_id',
                'company_id',
                'attendance',
                'halfday',
                'date',
                'in_time',
                'out_time',
                'company_id',
            ]);
    }

    private function exportExcel($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $attendances = $this->getFilteredAttendances($startDate, $endDate, $companyId, $departmentId, $branchId);
        $company = CompanyDetail::find($companyId);
        $companyName = $company ? $company->company_name : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Company Name in Row 1
        $sheet->setCellValue('A1', 'Company: ' . $companyName);
        $sheet->mergeCells('A1:J1');

        // Header Row
        $headers = [
            'A2' => 'Employee ID',
            'B2' => 'Employee Name',
            'C2' => 'Attendance',
            'D2' => 'Half Day',
            'E2' => 'Date',
            'F2' => 'In Time',
            'G2' => 'Out Time',
            'H2' => 'Branch Name',
            'I2' => 'Department Name',
            'J2' => 'Company Name'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col, $header);
        }

        $row = 3; // Data starts from row 3
        foreach ($attendances as $a) {
            $sheet->setCellValue('A' . $row, $a->employee_id);
            $sheet->setCellValue('B' . $row, $a->employee->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $a->attendance);
            $sheet->setCellValue('D' . $row, $a->halfday);
            $sheet->setCellValue('E' . $row, Carbon::parse($a->date)->format('Y-m-d'));
            $sheet->setCellValue('F' . $row, $a->in_time ? Carbon::parse($a->in_time)->format('H:i:s') : 'N/A');
            $sheet->setCellValue('G' . $row, $a->out_time ? Carbon::parse($a->out_time)->format('H:i:s') : 'N/A');
            $sheet->setCellValue('H' . $row, $a->branch->branch_name ?? 'N/A');
            $sheet->setCellValue('I' . $row, $a->department->name ?? 'N/A');
            $sheet->setCellValue('J' . $row, $companyName);
            $row++;
        }

        $fileName = 'attendance_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        $filePath = public_path('uploads/excel/' . $fileName);

        if (!File::exists(public_path('uploads/excel'))) {
            File::makeDirectory(public_path('uploads/excel'), 0777, true);
        }

        (new Xlsx($spreadsheet))->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Attendance Excel file has been generated.',
            'download_url' => url('uploads/excel/' . $fileName)
        ]);
    }

    private function exportPdf($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $attendances = $this->getFilteredAttendances($startDate, $endDate, $companyId, $departmentId, $branchId);

        $companyName = CompanyDetail::find($companyId)->company_name ?? 'N/A';

        $data = [
            'attendances' => $attendances,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'companyName' => $companyName
        ];

        $pdf = app(PDF::class)->loadView('user.report.pdf_attendance', $data);

        $fileName = 'attendance_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        $filePath = public_path('uploads/pdf/' . $fileName);

        if (!File::exists(public_path('uploads/pdf'))) {
            File::makeDirectory(public_path('uploads/pdf'), 0777, true);
        }

        $pdf->save($filePath);

        return response()->json([
            'success' => true,
            'message' => 'Attendance PDF file has been generated.',
            'download_url' => url('uploads/pdf/' . $fileName)
        ]);
    }

    private function exportCsv($startDate, $endDate, $companyId, $departmentId, $branchId)
    {
        $attendances = $this->getFilteredAttendances($startDate, $endDate, $companyId, $departmentId, $branchId);
        $company = CompanyDetail::find($companyId);
        $companyName = $company ? $company->company_name : 'N/A';

        $folderPath = public_path('uploads/csv');
        $fileName = 'attendance_report_' . Carbon::parse($startDate)->format('Ymd') . '_to_' . Carbon::parse($endDate)->format('Ymd') . '.csv';
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        $csv = Writer::createFromPath($filePath, 'w+');

        // Add company name as first line
        $csv->insertOne(['Company: ' . $companyName]);
        $csv->insertOne([]); // Blank row

        // Header
        $csv->insertOne([
            'Employee ID',
            'Employee Name',
            'Attendance',
            'Half Day',
            'Date',
            'In Time',
            'Out Time',
            'Branch Name',
            'Department Name',
            'Company Name'
        ]);

        foreach ($attendances as $a) {
            $csv->insertOne([
                $a->employee_id,
                $a->employee->name ?? 'N/A',
                $a->attendance,
                $a->halfday,
                Carbon::parse($a->date)->format('Y-m-d'),
                $a->in_time ? Carbon::parse($a->in_time)->format('H:i:s') : 'N/A',
                $a->out_time ? Carbon::parse($a->out_time)->format('H:i:s') : 'N/A',
                $a->branch->branch_name ?? 'N/A',
                $a->department->name ?? 'N/A',
                $companyName
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance CSV file has been generated.',
            'download_url' => url('uploads/csv/' . $fileName)
        ]);
    }


    public function getAllAttendanceReports(Request $request)
    {
        $companyId = $request->company_id;

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Missing company_id parameter.'
            ], 201);
        }

        // Fetch all distinct attendance dates for this company
        $attendanceDates = Attendance::where('company_id', $companyId)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Ymd');
            })
            ->toArray();

        // dd($attendanceDates);

        if (empty($attendanceDates)) {
            return response()->json([
                'success' => true,
                'message' => 'No attendance data found for this company.',
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

                // Only match attendance_report_* files
                if (str_starts_with($fileName, 'attendance_report_')) {
                    foreach ($attendanceDates as $date) {
                        $modifiedTime = File::lastModified($file);
                        $reportList[] = [
                            'file_name'    => "attendance_report",
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

        usort($reportList, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));

        return response()->json([
            'success' => true,
            'message' => 'Attendance reports generated for this company.',
            'data'    => $reportList
        ], 200);
    }
}