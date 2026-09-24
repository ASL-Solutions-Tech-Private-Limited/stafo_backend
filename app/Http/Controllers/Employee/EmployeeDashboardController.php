<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeLeave;
use App\Models\EmployeePunch;
use App\Models\Holiday;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\EmployeeSalarySummary;
use App\Models\EmployeeDocument;
use App\Models\DeviceSession;
use App\Models\Leavetype;
use App\Models\Branch;
use App\Models\CompanyDetail;
use App\Models\BankAccount;
use App\Models\Notification;
use App\Helpers\Helper;
use App\Exports\SalaryPDFExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Exception;

class EmployeeDashboardController extends Controller
{
    /**
     * Get authenticated employee
     */
    protected function getAuthEmployee()
    {
        return Auth::guard('employee')->user();
    }

    /**
     * Display the employee dashboard
     */
    public function index()
    {
        $employee = $this->getAuthEmployee();
        if (!$employee) {
            return redirect()->route('login');
        }

        $employee_info = Employee::with(['company', 'branch', 'department', 'shift', 'employeeType'])->find($employee->id);
        $company_id = $employee_info->company_id;

        $today = date('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Today's Attendance record
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Today's Punches
        $todayPunches = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        $firstPunch = $todayPunches->first();
        $lastPunch = $todayPunches->last();
        $activePunch = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('punch_in', $today)
            ->whereNull('punch_out')
            ->latest()
            ->first();
        $isPunchedIn = !empty($activePunch);

        // Monthly Attendance stats
        $monthDays = Carbon::now()->daysInMonth;
        $presentDays = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $halfDays = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('halfday', 1)
            ->count();

        $approvedLeavesThisMonth = (float) EmployeeLeave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereMonth('from_date', $currentMonth)
            ->whereYear('from_date', $currentYear)
            ->sum('days');

        // Leave Balances (total allocated minus approved leaves taken this year)
        $totalCasual = (float) ($employee_info->casual_leave ?? 0);
        $totalSick = (float) ($employee_info->sick_leave ?? 0);
        $totalPrivileged = (float) ($employee_info->privileged_leave ?? 0);

        $takenCasual = (float) EmployeeLeave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->where(function ($q) {
                $q->where('leave_type', 'like', '%casual%')
                  ->orWhere('leave_type', '1');
            })
            ->sum('days');

        $takenSick = (float) EmployeeLeave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->where(function ($q) {
                $q->where('leave_type', 'like', '%sick%')
                  ->orWhere('leave_type', '2');
            })
            ->sum('days');

        $takenPrivileged = (float) EmployeeLeave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->where(function ($q) {
                $q->where('leave_type', 'like', '%privilege%')
                  ->orWhere('leave_type', 'like', '%earned%')
                  ->orWhere('leave_type', '3');
            })
            ->sum('days');

        $remainingCasual = max(0, $totalCasual - $takenCasual);
        $remainingSick = max(0, $totalSick - $takenSick);
        $remainingPrivileged = max(0, $totalPrivileged - $takenPrivileged);

        // Recent 7 days attendance
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        // Tasks assigned to this employee
        $assignedTasks = Task::whereHas('assignedEmployees', function ($q) use ($employee) {
            $q->where('employees.id', $employee->id);
        })
        ->latest()
        ->take(5)
        ->get();

        $pendingTasksCount = Task::whereHas('assignedEmployees', function ($q) use ($employee) {
            $q->where('employees.id', $employee->id);
        })
        ->whereIn('status', ['pending', 'in_progress', '0', '1', null])
        ->count();

        // Upcoming Holidays (fetch up to 8 so all upcoming festive holidays including Maha Dashmi are included)
        $upcomingHolidays = Holiday::where('company_id', $company_id)
            ->where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->take(8)
            ->get();

        // Teammates' birthdays & anniversaries this month
        $birthdays = Employee::select('id', 'name', 'date_of_birth', 'position', 'image', 'department_id')
            ->with('department')
            ->where('company_id', $company_id)
            ->where('status', '1')
            ->whereMonth('date_of_birth', date('m'))
            ->orderByRaw('DAY(date_of_birth) ASC')
            ->take(5)
            ->get();

        $anniversaries = Employee::select('id', 'name', 'date_of_joining', 'position', 'image', 'department_id')
            ->with('department')
            ->where('company_id', $company_id)
            ->where('status', '1')
            ->whereMonth('date_of_joining', date('m'))
            ->orderByRaw('DAY(date_of_joining) ASC')
            ->take(5)
            ->get();

        // Recent leave applications
        $myLeaves = EmployeeLeave::where('employee_id', $employee->id)
            ->latest()
            ->take(4)
            ->get();

        return view('employee.dashboard', compact(
            'employee_info',
            'todayAttendance',
            'todayPunches',
            'firstPunch',
            'lastPunch',
            'presentDays',
            'halfDays',
            'approvedLeavesThisMonth',
            'monthDays',
            'totalCasual',
            'totalSick',
            'totalPrivileged',
            'takenCasual',
            'takenSick',
            'takenPrivileged',
            'remainingCasual',
            'remainingSick',
            'remainingPrivileged',
            'recentAttendances',
            'assignedTasks',
            'pendingTasksCount',
            'upcomingHolidays',
            'birthdays',
            'anniversaries',
            'myLeaves',
            'activePunch',
            'isPunchedIn'
        ));
    }

    /**
     * Display employee profile
     */
    public function profile()
    {
        $employee = $this->getAuthEmployee();
        $employee = Employee::with(['company', 'branch', 'department', 'shift', 'employeeType', 'bankAccount', 'country', 'state', 'city', 'documents'])->findOrFail($employee->id);

        if (!$employee->hasPermission('profile.view')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to My Profile.');
        }

        return view('employee.profile', compact('employee'));
    }

    /**
     * Update employee's own profile details
     */
    public function updateProfile(Request $request)
    {
        $authEmp = $this->getAuthEmployee();
        if (!$authEmp) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $employee = Employee::with('bankAccount')->findOrFail($authEmp->id);

        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'nullable|email|max:191|unique:employees,email,' . $employee->id,
            'phone' => 'required|digits:10|unique:employees,phone,' . $employee->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,Male,Female,Other',
            'marital_status' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
            'guardian_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'pin' => 'nullable|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',

            // Bank details
            'bank_name' => 'nullable|string|max:150',
            'account_holder_name' => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:30',
            'ifsc_code' => 'nullable|string|max:20',
            'branch_name' => 'nullable|string|max:150',

            // Statutory details
            'uan' => 'nullable|string|max:30',
            'pf_number' => 'nullable|string|max:30',
            'esi_number' => 'nullable|string|max:30',
            'aadhar' => 'nullable|string|max:20',
            'pan' => 'nullable|string|max:20',
        ]);

        // 1. Update Profile Image if uploaded
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = 'emp_' . $employee->id . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('uploads/employees'), $imageName);
            $employee->image = $imageName;
        }

        // 2. Update Personal Fields
        $employee->name = trim($request->name);
        $employee->phone = trim($request->phone);
        if ($request->filled('email')) {
            $employee->email = trim($request->email);
        }
        if ($request->filled('date_of_birth')) {
            $employee->date_of_birth = $request->date_of_birth;
        }
        if ($request->filled('gender')) {
            $employee->gender = strtolower($request->gender);
        }
        if ($request->filled('marital_status')) {
            $employee->marital_status = $request->marital_status;
        }
        if ($request->filled('blood_group')) {
            $employee->blood_group = $request->blood_group;
        }
        if ($request->filled('guardian_name')) {
            $employee->guardian_name = $request->guardian_name;
        }
        if ($request->has('address')) {
            $employee->address = $request->address;
        }
        if ($request->has('pin')) {
            $employee->pin = $request->pin;
        }
        if ($request->filled('uan')) {
            $employee->uan = trim($request->uan);
        }
        if ($request->filled('pf_number')) {
            $employee->pf_number = trim($request->pf_number);
        }
        if ($request->filled('esi_number')) {
            $employee->esi_number = trim($request->esi_number);
        }

        // Aadhaar and PAN (only allow edit if not already verified by company)
        if ($request->filled('aadhar') && strtolower((string)$employee->aadhar_verify) !== 'yes') {
            $employee->aadhar = trim($request->aadhar);
        }
        if ($request->filled('pan') && strtolower((string)$employee->pan_verify) !== 'yes') {
            $employee->pan = strtoupper(trim($request->pan));
        }

        $employee->save();

        // 3. Update Bank Details if provided
        if ($request->filled('bank_name') || $request->filled('account_number') || $request->filled('ifsc_code') || $request->filled('account_holder_name') || $request->filled('branch_name')) {
            $bankAccount = BankAccount::firstOrNew(['employee_id' => $employee->id]);
            if ($request->filled('bank_name')) {
                $bankAccount->bank_name = trim($request->bank_name);
            }
            if ($request->filled('account_holder_name')) {
                $bankAccount->account_holder_name = trim($request->account_holder_name);
            }
            if ($request->filled('account_number')) {
                $bankAccount->account_number = trim($request->account_number);
            }
            if ($request->filled('ifsc_code')) {
                $bankAccount->ifsc_code = strtoupper(trim($request->ifsc_code));
            }
            if ($request->filled('branch_name')) {
                $bankAccount->branch_name = trim($request->branch_name);
            }
            $bankAccount->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully! Your details have been saved.'
            ]);
        }

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully! Your details have been saved.');
    }

    /**
     * Display monthly attendance history
     */
    public function attendance(Request $request)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee->hasPermission('attendance.view') && !$employee->hasPermission('attendance.history') && !$employee->hasPermission('attendance.punch')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Attendance.');
        }

        $month = sprintf('%02d', (int) $request->input('month', date('m')));
        $year = (int) $request->input('year', date('Y'));
        $today = date('Y-m-d');

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->paginate(31);

        // Keyed attendances for fast calendar lookup
        $monthlyAttendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $presentCount = $monthlyAttendances->count();

        $halfDayCount = $monthlyAttendances->where('halfday', 1)->count();

        // Company Holidays for selected month
        $company_id = $employee->company_id;
        $monthlyHolidays = collect();
        $holidayDates = [];
        if ($company_id) {
            $monthlyHolidays = Holiday::where('company_id', $company_id)
                ->where(function($q) use ($year, $month) {
                    $q->whereYear('start_date', $year)->whereMonth('start_date', $month)
                      ->orWhere(function($sq) use ($year, $month) {
                          $sq->whereYear('end_date', $year)->whereMonth('end_date', $month);
                      });
                })
                ->get();

            foreach ($monthlyHolidays as $h) {
                $sDate = Carbon::parse($h->start_date);
                $eDate = !empty($h->end_date) ? Carbon::parse($h->end_date) : $sDate;
                for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                    if ($d->format('m') == $month && $d->format('Y') == $year) {
                        $holidayDates[$d->format('Y-m-d')] = $h;
                    }
                }
            }
        }

        // Approved Leaves for selected month
        $approvedLeaves = EmployeeLeave::with('leavetype')
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function($q) use ($year, $month) {
                $q->whereYear('from_date', $year)->whereMonth('from_date', $month)
                  ->orWhere(function($sq) use ($year, $month) {
                      $sq->whereYear('to_date', $year)->whereMonth('to_date', $month);
                  });
            })
            ->get();

        $leaveDates = [];
        foreach ($approvedLeaves as $l) {
            $sDate = Carbon::parse($l->from_date);
            $eDate = !empty($l->to_date) ? Carbon::parse($l->to_date) : $sDate;
            for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                if ($d->format('m') == $month && $d->format('Y') == $year) {
                    $leaveDates[$d->format('Y-m-d')] = $l;
                }
            }
        }

        // Punches for the month grouped by date
        $monthlyPunches = EmployeePunch::where('employee_id', $employee->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function($p) {
                return Carbon::parse($p->created_at)->format('Y-m-d');
            });

        // Compute monthly absent days and weekend count
        $startOfMonth = Carbon::createFromDate($year, (int)$month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, (int)$month, 1)->endOfMonth();
        $daysInMonth = $endOfMonth->day;
        
        $absentCount = 0;
        $weekendCount = 0;
        $todayDate = Carbon::today();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, (int)$month, $day);
            $dateStr = $currentDate->format('Y-m-d');

            if ($currentDate->isSunday()) {
                $weekendCount++;
                continue;
            }

            // If it's a past date (or today without attendance)
            if ($currentDate->lte($todayDate)) {
                $hasAtt = isset($monthlyAttendances[$dateStr]);
                $hasHol = isset($holidayDates[$dateStr]);
                $hasLeave = isset($leaveDates[$dateStr]);

                if (!$hasAtt && !$hasHol && !$hasLeave) {
                    if ($currentDate->lt($todayDate)) {
                        $absentCount++;
                    }
                }
            }
        }

        $punches = EmployeePunch::where('employee_id', $employee->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->latest()
            ->take(50)
            ->get();

        // Today's punches for live clock & punch widgets
        $todayPunches = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        $firstPunch = $todayPunches->first();
        $lastPunch = $todayPunches->last();
        $activePunch = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('punch_in', $today)
            ->whereNull('punch_out')
            ->latest()
            ->first();
        $isPunchedIn = !empty($activePunch);
        $employee_info = Employee::with(['company', 'branch', 'shift'])->find($employee->id);

        return view('employee.attendance', compact(
            'attendances', 
            'monthlyAttendances',
            'presentCount', 
            'halfDayCount', 
            'absentCount',
            'weekendCount',
            'holidayDates',
            'leaveDates',
            'monthlyPunches',
            'daysInMonth',
            'month', 
            'year', 
            'punches',
            'todayPunches',
            'firstPunch',
            'lastPunch',
            'activePunch',
            'isPunchedIn',
            'employee_info'
        ));
    }

    /**
     * Display leaves list & application form
     */
    public function leaves()
    {
        $employee = $this->getAuthEmployee();
        if (!$employee->hasPermission('leaves.view') && !$employee->hasPermission('leaves.apply')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Leave Requests.');
        }

        $employee_info = Employee::find($employee->id);
        $currentYear = Carbon::now()->year;

        $leaves = EmployeeLeave::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $leaveTypes = Leavetype::where('company_id', $employee_info->company_id)->get();

        return view('employee.leaves', compact('leaves', 'employee_info', 'leaveTypes'));
    }

    /**
     * Apply for leave
     */
    public function applyLeave(Request $request)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee->hasPermission('leaves.apply')) {
            return redirect()->route('employee.leaves')->with('error', 'Access Denied: You do not have permission to apply for leaves.');
        }

        $employee_info = Employee::findOrFail($employee->id);

        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|string',
            'reason' => 'required|string|max:500',
        ]);

        $from = Carbon::parse($request->from_date);
        $to = Carbon::parse($request->to_date);
        $days = $from->diffInDays($to) + 1;

        EmployeeLeave::create([
            'company_id' => $employee_info->company_id,
            'branch_id' => $employee_info->branch_id,
            'department_id' => $employee_info->department_id,
            'employee_id' => $employee_info->id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'leave_type' => $request->leave_type,
            'days' => $days,
            'status' => 'pending',
        ]);

        return redirect()->route('employee.leaves')->with('success', 'Leave application submitted successfully!');
    }

    /**
     * Display assigned tasks
     */
    public function tasks()
    {
        $employee = $this->getAuthEmployee();
        if (!$employee->hasPermission('tasks.view')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Tasks.');
        }

        $tasks = Task::whereHas('assignedEmployees', function ($q) use ($employee) {
            $q->where('employees.id', $employee->id);
        })
        ->with('taskFiles')
        ->latest()
        ->paginate(15);

        return view('employee.tasks', compact('tasks'));
    }

    /**
     * Update task status
     */
    public function updateTaskStatus(Request $request, $id)
    {
        $employee = $this->getAuthEmployee();

        if (!$employee->hasPermission('tasks.update_status')) {
            return back()->with('error', 'Access Denied: You do not have permission to update task status.');
        }

        $task = Task::whereHas('assignedEmployees', function ($q) use ($employee) {
            $q->where('employees.id', $employee->id);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        $task->status = $request->status;
        $task->save();

        return back()->with('success', 'Task status updated successfully!');
    }

    /**
     * Display salary slips
     */
    public function salarySlips()
    {
        $employee = $this->getAuthEmployee();
        $employee_info = Employee::with('company')->findOrFail($employee->id);

        if (!$employee_info->hasPermission('payroll.self') && !$employee_info->hasPermission('payroll.view') && !$employee_info->hasPermission('payroll.download')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Salary Slips.');
        }

        $salarySummaries = EmployeeSalarySummary::where('employee_id', $employee->id)
            ->latest()
            ->paginate(12);

        return view('employee.salary_slips', compact('salarySummaries', 'employee_info'));
    }

    /**
     * Download salary slip PDF
     */
    public function downloadSalarySlip(Request $request, $id)
    {
        $employee = $this->getAuthEmployee();
        $employee_info = Employee::findOrFail($employee->id);

        if (!$employee_info->hasPermission('payroll.self') && !$employee_info->hasPermission('payroll.download')) {
            return redirect()->route('employee.salarySlips')->with('error', 'Access Denied: You do not have permission to download salary slips.');
        }

        $summary = EmployeeSalarySummary::where('employee_id', $employee->id)
            ->findOrFail($id);

        $month = $summary->salary_month;
        $year = $summary->salary_year;
        $company = CompanyDetail::find($employee_info->company_id);
        $emp_id = $employee_info->id;

        $export = new SalaryPDFExport($company, $emp_id, $month, $year);
        $fileName = 'SalarySlip_' . preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $employee_info->name)) . '_' . $month . '_' . $year . '.pdf';

        return $export->download($fileName);
    }

    /**
     * Display employee documents & KYC
     */
    public function documents()
    {
        $employee = $this->getAuthEmployee();
        $employee_info = Employee::with('documents')->findOrFail($employee->id);

        if (!$employee_info->hasPermission('documents.view')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Documents.');
        }

        return view('employee.documents', compact('employee_info'));
    }

    /**
     * Update employee document field (Aadhar, PAN, Voter ID, Driving License)
     */
    public function updateDocumentData(Request $request)
    {
        $employee = $this->getAuthEmployee();
        $employee_info = Employee::findOrFail($employee->id);

        $allowedTypes = ['aadhar', 'pan', 'voter', 'driving_license'];
        $type = $request->input('type');
        $data = trim((string)$request->input('data'));

        if (!in_array($type, $allowedTypes)) {
            return response()->json(['success' => false, 'message' => 'Invalid document type.'], 400);
        }

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'Please provide a valid document number.'], 422);
        }

        // Check if already verified - verified documents can never be modified
        $verifyField = match ($type) {
            'aadhar' => 'aadhar_verify',
            'pan' => 'pan_verify',
            'voter' => 'voter_verify',
            'driving_license' => 'dl_verify',
            default => null
        };

        if ($verifyField && ($employee_info->{$verifyField} === 'Yes' || $employee_info->{$verifyField} === '1' || strtolower((string)$employee_info->{$verifyField}) === 'yes')) {
            return response()->json([
                'success' => false,
                'message' => 'This document is already verified and cannot be modified.'
            ], 422);
        }

        $employee_info->{$type} = $data;
        $employee_info->save();

        return response()->json([
            'success' => true,
            'message' => 'Document details updated successfully.'
        ]);
    }

    /**
     * Display holidays
     */
    public function holidays()
    {
        $employee = $this->getAuthEmployee();
        $employee_info = Employee::findOrFail($employee->id);

        if (!$employee_info->hasPermission('holidays.view')) {
            return redirect()->route('employee.dashboard')->with('error', 'Access Denied: Your company has not granted you access to Holiday Calendar.');
        }

        $holidays = Holiday::where('company_id', $employee_info->company_id)
            ->orderBy('start_date', 'asc')
            ->get();

        return view('employee.holidays', compact('holidays'));
    }

    /**
     * Calculate Distance between two lat/lng points in meters (Haversine formula)
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius in meters
        $latFrom = deg2rad((float)$lat1);
        $lonFrom = deg2rad((float)$lon1);
        $latTo = deg2rad((float)$lat2);
        $lonTo = deg2rad((float)$lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Web Punch IN / Punch OUT with Geolocation & Radar Validation
     */
    public function webPunch(Request $request)
    {
        try {
            $employee = $this->getAuthEmployee();
            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Unauthorized session.'], 401);
            }

            $employee_info = Employee::with(['company', 'branch'])->find($employee->id);
            if (!$employee_info->hasPermission('attendance.punch')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access Denied: Your company has disabled punch attendance for your account.'
                ], 403);
            }
            $branch = $employee_info->branch;

            // Enforce company-configured attendance_type
            $assignedType = strtolower(trim($employee_info->attendance_type ?? 'geo'));
            if ($assignedType === 'qr') {
                $assignedType = 'qr code';
            }
            if ($assignedType !== 'geo') {
                $modeName = $assignedType === 'selfie' ? 'Selfie / Camera' : 'Company QR Code';
                return response()->json([
                    'status' => false,
                    'message' => 'Attendance mode restricted: Your company has assigned your attendance mode to ' . $modeName . '. Please use ' . $modeName . ' to record your attendance.'
                ], 422);
            }

            // Format coordinates with 7 decimal precision matching database/mobile format
            $lat = $request->filled('latitude') ? number_format((float)$request->input('latitude'), 7, '.', '') : null;
            $lng = $request->filled('longitude') ? number_format((float)$request->input('longitude'), 7, '.', '') : null;

            // Check branch geofence/radar if branch coordinates are defined
            if ($branch && !empty($branch->latitude) && !empty($branch->longitude) && !empty($branch->radar) && $branch->radar > 0) {
                if (empty($lat) || empty($lng)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Office requires location coordinates for attendance punch. Please enable browser location/GPS and try again.'
                    ], 422);
                }

                $distance = $this->calculateDistance($lat, $lng, $branch->latitude, $branch->longitude);
                if ($distance > $branch->radar) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are outside the office geofence (' . round($distance) . 'm away, allowed radius: ' . round($branch->radar) . 'm).'
                    ], 422);
                }
            }

            $currentDate = now()->toDateString();
            $nowTime = now();

            // Check if active punch-in exists today without punch-out
            $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                ->whereDate('punch_in', $currentDate)
                ->whereNull('punch_out')
                ->latest()
                ->first();

            if ($existingPunch) {
                // Punch OUT
                $existingPunch->update([
                    'punch_out' => $nowTime,
                ]);

                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'out_time' => $nowTime,
                    ]);
                }

                // Push Notifications
                if (!empty($employee_info->fcm_token)) {
                    Helper::sendPushNotification($employee_info->fcm_token, "You have successfully punched out on Web at " . $nowTime->format('h:i A'));
                }
                if ($employee_info->company && !empty($employee_info->company->fcm_token)) {
                    Helper::sendPushNotification($employee_info->company->fcm_token, $employee_info->name . " successfully punched out on Web");
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_out',
                    'message' => 'Punched Out successfully at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => false,
                ]);
            } else {
                // Punch IN
                $punchIn = EmployeePunch::create([
                    'employee_id' => $employee->id,
                    'punch_in' => $nowTime,
                ]);

                $existsAttendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if (!$existsAttendance) {
                    Attendance::create([
                        'company_id' => $employee_info->company_id,
                        'branch_id' => $employee_info->branch_id,
                        'department_id' => $employee_info->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present',
                        'date' => $currentDate,
                        'in_time' => $nowTime,
                        'out_time' => null,
                    ]);
                }

                // Push Notifications
                if (!empty($employee_info->fcm_token)) {
                    Helper::sendPushNotification($employee_info->fcm_token, "You have successfully punched in on Web at " . $nowTime->format('h:i A'));
                }
                if ($employee_info->company && !empty($employee_info->company->fcm_token)) {
                    Helper::sendPushNotification($employee_info->company->fcm_token, $employee_info->name . " successfully punched in on Web");
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_in',
                    'message' => 'Punched In successfully at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => true,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during punch: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Web Selfie Attendance Punch
     */
    public function webSelfiePunch(Request $request)
    {
        try {
            $employee = $this->getAuthEmployee();
            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Unauthorized session.'], 401);
            }

            $employee_info = Employee::with(['company', 'branch'])->find($employee->id);
            if (!$employee_info->hasPermission('attendance.punch')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access Denied: Your company has disabled punch attendance for your account.'
                ], 403);
            }

            // Enforce company-configured attendance_type
            $assignedType = strtolower(trim($employee_info->attendance_type ?? 'geo'));
            if ($assignedType === 'qr') {
                $assignedType = 'qr code';
            }
            if ($assignedType !== 'selfie') {
                $modeName = $assignedType === 'qr code' ? 'Company QR Code' : 'Geo Location';
                return response()->json([
                    'status' => false,
                    'message' => 'Selfie attendance not allowed: Your company has assigned your attendance mode to ' . $modeName . '.'
                ], 422);
            }

            $currentDate = now()->toDateString();
            $nowTime = now();

            $punchinDir = public_path('uploads/employees/punchin');
            $punchoutDir = public_path('uploads/employees/punchout');
            if (!file_exists($punchinDir)) {
                @mkdir($punchinDir, 0777, true);
            }
            if (!file_exists($punchoutDir)) {
                @mkdir($punchoutDir, 0777, true);
            }

            $imageName = '';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'web_selfie_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $image->move($punchinDir, $imageName);
            } elseif ($request->filled('image_base64')) {
                $base64 = $request->input('image_base64');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                    $type = strtolower($type[1]);
                    $base64Data = base64_decode($base64);
                    if ($base64Data !== false) {
                        $imageName = 'web_selfie_' . time() . '_' . Str::random(6) . '.' . ($type === 'jpeg' ? 'jpg' : $type);
                        file_put_contents($punchinDir . '/' . $imageName, $base64Data);
                        @chmod($punchinDir . '/' . $imageName, 0666);
                    }
                }
            }

            if (empty($imageName)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selfie image is required to complete selfie attendance.'
                ], 422);
            }

            // Check active punch-in
            $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                ->whereDate('punch_in', $currentDate)
                ->whereNull('punch_out')
                ->latest()
                ->first();

            if ($existingPunch) {
                // Punch OUT with selfie
                $existingPunch->update([
                    'punch_out' => $nowTime,
                ]);

                if (file_exists(public_path('uploads/employees/punchin/' . $imageName))) {
                    @copy(public_path('uploads/employees/punchin/' . $imageName), public_path('uploads/employees/punchout/' . $imageName));
                }

                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'out_time' => $nowTime,
                        'punchout_image' => $imageName,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_out',
                    'message' => 'Selfie Punch Out recorded successfully at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => false,
                    'image' => asset('uploads/employees/punchin/' . $imageName),
                ]);
            } else {
                // Punch IN with selfie
                $punchIn = EmployeePunch::create([
                    'employee_id' => $employee->id,
                    'punch_in' => $nowTime,
                ]);

                $existsAttendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if (!$existsAttendance) {
                    Attendance::create([
                        'company_id' => $employee_info->company_id,
                        'branch_id' => $employee_info->branch_id,
                        'department_id' => $employee_info->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present',
                        'date' => $currentDate,
                        'in_time' => $nowTime,
                        'out_time' => null,
                        'punchin_image' => $imageName,
                    ]);
                } else {
                    $existsAttendance->update([
                        'punchin_image' => $imageName,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_in',
                    'message' => 'Selfie Punch In recorded successfully at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => true,
                    'image' => asset('uploads/employees/punchin/' . $imageName),
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error recording selfie attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Web QR Code Attendance Punch
     */
    public function webQrPunch(Request $request)
    {
        try {
            $employee = $this->getAuthEmployee();
            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Unauthorized session.'], 401);
            }

            $rawQr = trim($request->input('qrcode', ''));
            if (empty($rawQr)) {
                return response()->json(['status' => false, 'message' => 'QR Code data is empty.'], 422);
            }

            $employee_info = Employee::with('company')->find($employee->id);
            if (!$employee_info->hasPermission('attendance.punch')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access Denied: Your company has disabled punch attendance for your account.'
                ], 403);
            }

            // Enforce company-configured attendance_type
            $assignedType = strtolower(trim($employee_info->attendance_type ?? 'geo'));
            if ($assignedType === 'qr') {
                $assignedType = 'qr code';
            }
            if ($assignedType !== 'qr code') {
                $modeName = $assignedType === 'selfie' ? 'Selfie / Camera' : 'Geo Location';
                return response()->json([
                    'status' => false,
                    'message' => 'QR Code attendance not allowed: Your company has assigned your attendance mode to ' . $modeName . '.'
                ], 422);
            }

            $isValidCompany = false;

            // 1. Try base64 json decode
            $decoded = @base64_decode($rawQr);
            if ($decoded) {
                $json = @json_decode($decoded);
                if ($json && isset($json->company_id) && $json->company_id == $employee_info->company_id) {
                    $isValidCompany = true;
                }
            }

            // 2. Direct JSON check
            if (!$isValidCompany) {
                $json = @json_decode($rawQr);
                if ($json && isset($json->company_id) && $json->company_id == $employee_info->company_id) {
                    $isValidCompany = true;
                }
            }

            // 3. Direct match with company_id or company_name
            if (!$isValidCompany) {
                if ($rawQr == (string)$employee_info->company_id || 
                    strtolower($rawQr) === strtolower($employee_info->company->company_name ?? '')) {
                    $isValidCompany = true;
                }
            }

            if (!$isValidCompany) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid QR Code. This QR code does not match your company.'
                ], 422);
            }

            $currentDate = now()->toDateString();
            $nowTime = now();

            $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                ->whereDate('punch_in', $currentDate)
                ->whereNull('punch_out')
                ->latest()
                ->first();

            if ($existingPunch) {
                $existingPunch->update(['punch_out' => $nowTime]);

                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();
                if ($attendance) {
                    $attendance->update(['out_time' => $nowTime]);
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_out',
                    'message' => 'QR Code Punch Out recorded at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => false,
                ]);
            } else {
                $punchIn = EmployeePunch::create([
                    'employee_id' => $employee->id,
                    'punch_in' => $nowTime,
                ]);

                $existsAttendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if (!$existsAttendance) {
                    Attendance::create([
                        'company_id' => $employee_info->company_id,
                        'branch_id' => $employee_info->branch_id,
                        'department_id' => $employee_info->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present',
                        'date' => $currentDate,
                        'in_time' => $nowTime,
                        'out_time' => null,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'type' => 'punch_in',
                    'message' => 'QR Code Punch In recorded at ' . $nowTime->format('h:i A') . '!',
                    'time' => $nowTime->format('h:i A'),
                    'is_punched_in' => true,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing QR punch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Web Punch Status JSON Endpoint
     */
    public function webPunchStatus()
    {
        $employee = $this->getAuthEmployee();
        if (!$employee) {
            return response()->json(['status' => false], 401);
        }

        $today = date('Y-m-d');
        $todayPunches = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'asc')
            ->get();

        $activePunch = EmployeePunch::where('employee_id', $employee->id)
            ->whereDate('punch_in', $today)
            ->whereNull('punch_out')
            ->latest()
            ->first();

        $firstPunch = $todayPunches->first();
        $lastPunch = $todayPunches->last();

        return response()->json([
            'status' => true,
            'is_punched_in' => !empty($activePunch),
            'first_punch_in' => $firstPunch ? Carbon::parse($firstPunch->created_at)->format('h:i A') : null,
            'last_punch_out' => ($lastPunch && $lastPunch->punch_out) ? Carbon::parse($lastPunch->punch_out)->format('h:i A') : null,
            'punches_count' => $todayPunches->count(),
            'punches' => $todayPunches->map(function ($p, $idx) {
                return [
                    'index' => $idx + 1,
                    'punch_in' => $p->punch_in ? Carbon::parse($p->punch_in)->format('h:i:s A') : null,
                    'punch_out' => $p->punch_out ? Carbon::parse($p->punch_out)->format('h:i:s A') : null,
                ];
            }),
        ]);
    }

    /**
     * Respond to Geo Tracking Request (Accept or Decline)
     */
    public function respondGeoTracking(Request $request)
    {
        try {
            $employee = $this->getAuthEmployee();
            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Unauthorized.'], 401);
            }

            $action = $request->input('action'); // 'accept' or 'decline'
            if (!in_array($action, ['accept', 'decline'])) {
                return response()->json(['status' => false, 'message' => 'Invalid action.'], 422);
            }

            $empModel = Employee::with('company')->find($employee->id);
            if (!$empModel) {
                return response()->json(['status' => false, 'message' => 'Employee not found.'], 404);
            }

            if ($action === 'accept') {
                $empModel->geo_status = '2';
                $empModel->save();

                // Create in-app notification for company
                Notification::create([
                    'employee_id' => $empModel->id,
                    'company_id' => $empModel->company_id,
                    'message' => $empModel->name . ' has accepted your real-time location tracking request.',
                    'status' => 'unread',
                ]);

                // Send push notification to company if available
                if ($empModel->company && !empty($empModel->company->fcm_token)) {
                    try {
                        Helper::sendPushNotification($empModel->company->fcm_token, $empModel->name . ' accepted real-time location tracking.');
                    } catch (\Throwable $t) {}
                }

                return response()->json([
                    'status' => true,
                    'geo_status' => '2',
                    'message' => 'Real-time location tracking request accepted successfully. Live location sharing is now active.',
                ]);
            } else {
                $empModel->geo_status = '0';
                $empModel->save();

                Notification::create([
                    'employee_id' => $empModel->id,
                    'company_id' => $empModel->company_id,
                    'message' => $empModel->name . ' has declined or disabled real-time location tracking.',
                    'status' => 'unread',
                ]);

                return response()->json([
                    'status' => true,
                    'geo_status' => '0',
                    'message' => 'Location tracking has been turned off / declined.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
