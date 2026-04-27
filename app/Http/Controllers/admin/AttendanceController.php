<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CompanyDetail;
use App\Models\Branch;
use App\Models\Employee;
use RealRashid\SweetAlert\Facades\Alert;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use App\Imports\AttendanceImport;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Response;

class AttendanceController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'attendance_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('attendance_file');
            $import = new AttendanceImport;

            // Import the file
            $import->import($file);

            // Check if any failure occurred
            if ($import->failureOccurred()) {
                // If any failure occurred, show an error message
                Alert::error('Error', 'There was an issue while importing the attendance data.');
            } else {
                // If no failure occurred, show success
                Alert::success('Success', 'Attendance data imported successfully.');
            }

            return back();
        } catch (\Exception $e) {

            return back();
        }
    }
    // Show all attendance records
    public function index(Request $request)
    {
        $attendances = Attendance::query()
            ->when($request->from_date && $request->to_date, fn($q) => $q->whereBetween('date', [$request->from_date, $request->to_date]))
            ->when($request->company_id, fn($q) => $q->whereRelation('company', 'id', $request->company_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->attendance, fn($q) => $q->where('attendance', $request->attendance))
            ->when($request->employee_search, fn($q) => $q->whereRelation('employee', 'name', 'like', '%' . $request->employee_search . '%'))
            ->with(['company', 'branch', 'employee'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.attendances.index', [
            'attendances' => $attendances,
            'companies' => CompanyDetail::all(),
            'branches' => Branch::all(),
            'request' => $request
        ]);
    }


    // Show form to create attendance
    public function create()
    {
        $companies = CompanyDetail::all();
        $branches = Branch::all();
        $employees = Employee::all();
        return view('admin.attendances.create', compact('companies', 'branches', 'employees'));
    }

    // Store new attendance record
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'attendance' => 'required|in:Present,Absent,Leave',
            'halfday' => 'boolean',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);

        Attendance::create($request->all());
        Alert::success('Success', 'Attendance recorded successfully.');
        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully.');
    }

    // Show attendance details
    public function show($id)
    {
        $attendance = Attendance::with(['company', 'branch', 'employee'])->findOrFail($id);
        return view('admin.attendances.show', compact('attendance'));
    }
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        $companies = CompanyDetail::all();
        $branches = Branch::all();
        $employees = Employee::all();
        return view('admin.attendances.edit', compact('attendance', 'companies', 'branches', 'employees'));
    }
    public function update(Request $request, $id)
    {
        // Validation of the input fields
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'attendance' => 'required|string',
            'halfday' => 'nullable|boolean',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);

        // Finding the attendance record by id
        $attendance = Attendance::findOrFail($id);

        // Updating only the fields that need to be updated
        $attendance->update([
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'employee_id' => $request->employee_id,
            'attendance' => $request->attendance,
            'halfday' => $request->halfday ?? 0,  // Default to 0 if not provided
            'date' => $request->date,
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
        ]);
        Alert::success('Success', 'Attendance Details  updated successfully.');

        // Redirecting with success message
        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        Alert::success('Success', 'Attendance  deleted successfully.');
        return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = Attendance::query()
            ->with(['company', 'branch', 'employee'])
            ->when($request->from_date && $request->to_date, fn($q) => $q->whereBetween('date', [$request->from_date, $request->to_date]))
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->attendance, fn($q) => $q->where('attendance', $request->attendance))
            ->when($request->employee_search, fn($q) => $q->whereHas('employee', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee_search . '%');
            }));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->fromArray([
            'EmpId',
            'Company',
            'Branch',
            'Employee',
            'Attendance',
            'Date',
            'In Time',
            'Out Time',
            'Half Day'
        ], null, 'A1');

        // Data
        $row = 2;
        foreach ($query->get() as $attendance) {
            $sheet->fromArray([
                $attendance->employee->emp_id,
                $attendance->company->company_name,
                isset($attendance->branch->branch_name)? $attendance->branch->branch_name : '',
                $attendance->employee->name,
                $attendance->attendance,
                $attendance->date,
                $attendance->in_time,
                $attendance->out_time,
                $attendance->halfday ? 'Yes' : 'No',
            ], null, "A{$row}");
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        // Return as a streamed response
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="attendances_export.xlsx"',
        ]);
    }
}