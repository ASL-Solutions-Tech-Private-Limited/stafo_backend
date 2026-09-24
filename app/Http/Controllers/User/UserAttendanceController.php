<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CompanyDetail;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Imports\AttendanceImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RealRashid\SweetAlert\Facades\Alert;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\AttendanceExport;


class UserAttendanceController extends Controller
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


    public function export(Request $request)
    {
        $companyId = Auth::id();
        $date = $request->date;
        $startDate = $request->from_date ?: $date;
        $endDate = $request->to_date ?: $date;
        $departmentId = $request->department;
        $branchId = $request->branch_id;
        $employeeId = $request->employee_id;

        if(!empty($startDate) && !empty($endDate)) {
            $export = new AttendanceExport($startDate, $endDate, $companyId, $departmentId, $branchId, $employeeId);
            return $export->export();
        }

        $query = Attendance::query()
            ->with(['company', 'branch', 'employee'])
            ->where('company_id', $companyId)
            ->when($request->date, fn($q) => $q->where('date', $request->date))
            ->when($request->from_date && $request->to_date, fn($q) => $q->whereBetween('date', [$request->from_date, $request->to_date]))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->attendance, fn($q) => $q->where('attendance', $request->attendance))
            ->when($request->employee_id, fn($query) => $query->where('employee_id', $request->employee_id));

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
                isset($attendance->branch->branch_name)? $attendance->branch->branch_name : 'N/A',
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



    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();
        $employeeId = $request->get('employee_id');
        $attendanceStatus = $request->get('attendance');

        $currentDate = Carbon::today()->format('Y-m-d');
        $allDates = $request->has('all_dates') && $request->get('all_dates') == '1';

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $date = $request->get('date');

        // If not filtering, default date to current date
        if ($allDates) {
            $date = null;
            $fromDate = null;
            $toDate = null;
        } elseif (!$request->filled('date') && !$request->filled('from_date') && !$request->filled('to_date')) {
            $date = $currentDate;
        }

        $attendancesQuery = Attendance::with(['employee', 'company', 'branch', 'department'])
            ->where('company_id', $companyId)
            ->when($employeeId, fn($query) => $query->where('employee_id', $employeeId))
            ->when($attendanceStatus, fn($query) => $query->where('attendance', $attendanceStatus));

        if ($fromDate && $toDate) {
            $attendancesQuery->whereBetween('date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            $attendancesQuery->where('date', '>=', $fromDate);
        } elseif ($toDate) {
            $attendancesQuery->where('date', '<=', $toDate);
        } elseif ($date) {
            $attendancesQuery->where('date', $date);
        }

        // Summary counts for the current filter
        $summaryCounts = (clone $attendancesQuery)->select(
            DB::raw('count(*) as total'),
            DB::raw("sum(case when attendance = 'Present' then 1 else 0 end) as present"),
            DB::raw("sum(case when attendance = 'Absent' then 1 else 0 end) as absent"),
            DB::raw("sum(case when attendance = 'Leave' then 1 else 0 end) as leave_count"),
            DB::raw("sum(case when halfday = 1 then 1 else 0 end) as halfday")
        )->first();

        $attendances = $attendancesQuery->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Fetch employees for the filter dropdown
        $employees = Employee::where('company_id', $companyId)->orderBy('name', 'asc')->get();

        return view('user.attendance.index', compact(
            'attendances',
            'employees',
            'date',
            'fromDate',
            'toDate',
            'currentDate',
            'allDates',
            'summaryCounts'
        ));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $userId = Auth::id();
        $branches = Branch::select('id', 'branch_name')
            ->where('company_id', $userId)
            ->where('status', 1)
            ->get();

        $departments = Department::select('id', 'name')
            ->where('company_id', $userId)
            ->where('status', 1)
            ->get();

        $employees = Employee::select('id', 'name')->where('company_id', $userId)->get();
        return view('user.attendance.create', compact('branches', 'employees', 'departments', 'userId'));
    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(Request $request)
    {
        // Validate Request Data
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|in:Present,Absent,Leave',
            'halfday' => 'nullable|boolean',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);
        $userId = Auth::id();
        // Create Attendance Entry
        $attendance = Attendance::create([
            'company_id' => $userId,
            'branch_id' => $validatedData['branch_id'],
            'employee_id' => $validatedData['employee_id'],
            'department_id' => $validatedData['department_id'],
            'attendance' => $validatedData['attendance'],
            'halfday' => $validatedData['halfday'] ?? 0,
            'date' => Carbon::parse($validatedData['date'])->format('Y-m-d'),
            'in_time' => $validatedData['in_time'] ?? null,
            'out_time' => $validatedData['out_time'] ?? null,
        ]);

        // Return success response
        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully');
    }
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $attendance = Attendance::findOrFail($id);
        $branches = Branch::select('id', 'branch_name')
            ->where('company_id', $userId)
            ->where('status', 1)
            ->get();

        $departments = Department::select('id', 'name')
            ->where('company_id', $userId)
            ->where('status', 1)
            ->get();

        $employees = Employee::select('id', 'name')->where('company_id', $userId)->get();

        return view('user.attendance.edit', compact('attendance',  'branches', 'employees', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|in:Present,Absent,Leave',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);
        $userId = Auth::id();
        // Find attendance record
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return back()->with('error', 'Attendance record not found.');
        }

        // Update attendance record
        $attendance->update([
            'company_id' => $userId, // Use the company_id from the logged-in user
            'branch_id' => $validatedData['branch_id'],
            'employee_id' => $validatedData['employee_id'],
            'department_id' => $validatedData['department_id'],
            'attendance' => $validatedData['attendance'],
            'date' => Carbon::parse($validatedData['date'])->format('Y-m-d'),
            'in_time' => $validatedData['in_time'] ?? null,
            'out_time' => $validatedData['out_time'] ?? null,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}