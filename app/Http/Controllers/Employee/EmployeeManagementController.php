<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\Attendance;
use App\Models\EmployeePunch;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\EmployeeSalarySummary;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Branch;
use App\Models\Shift;
use App\Models\Leavetype;
use App\Models\CompanyDetail;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeExport;
use App\Exports\EmployeePDFExport;
use App\Exports\AttendanceExport;
use App\Exports\AttendancePDFExport;
use App\Exports\LeaveExport;
use App\Exports\LeavePDFExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeManagementController extends Controller
{
    /**
     * Get the authenticated employee.
     */
    protected function getAuthEmployee()
    {
        return Auth::guard('employee')->user();
    }

    /**
     * Ensure the employee has the requested permission or abort/redirect.
     */
    protected function authorizePermission($permissionKey)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || !$employee->hasPermission($permissionKey)) {
            abort(403, 'Unauthorized. Your designation does not have permission to access this management module.');
        }
        return $employee;
    }

    /**
     * View Company Employee Directory
     */
    /**
     * View Company Employee Directory (Employees)
     */
    public function employees(Request $request)
    {
        $employee = $this->authorizePermission('employees.view');
        $companyId = $employee->company_id;

        $search = $request->input('search') ?: $request->input('name');
        $departmentId = $request->input('department_id');
        $designationId = $request->input('designation_id');
        $branchId = $request->input('branch_id');
        $status = $request->input('status');
        $kycStatus = $request->input('kyc_status');

        $query = Employee::where('company_id', $companyId)
            ->with(['department', 'designation', 'branch', 'shift', 'shifts']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('emp_id', 'like', "%{$search}%");
            });
        }

        if (!empty($departmentId)) {
            $query->where('department_id', $departmentId);
        }

        if (!empty($designationId)) {
            $query->where('designation_id', $designationId);
        }

        if (!empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        if ($status !== null && $status !== '') {
            $query->where('status', (string)$status);
        }

        if ($kycStatus === 'verified') {
            $query->where(function ($q) {
                $q->where('aadhar_verify', 'Yes')
                  ->orWhere('pan_verify', 'Yes')
                  ->orWhere('voter_verify', 'Yes')
                  ->orWhere('dl_verify', 'Yes');
            });
        } elseif ($kycStatus === 'unverified') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('aadhar_verify')->orWhere('aadhar_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('pan_verify')->orWhere('pan_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('voter_verify')->orWhere('voter_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('dl_verify')->orWhere('dl_verify', '!=', 'Yes');
                });
            });
        }

        $perPage = (int) $request->input('per_page', 12);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 12;
        }

        $employees = $query->orderBy('name', 'asc')->paginate($perPage)->withQueryString();

        $departments = Department::where('company_id', $companyId)->where('status', 1)->get();
        $designations = Designation::where('company_id', $companyId)->where('status', 1)->get();
        $branches = Branch::where('company_id', $companyId)->where('status', 1)->get();
        $shifts = Shift::where('company_id', $companyId)->get();

        $totalEmployees = Employee::where('company_id', $companyId)->count();
        $activeEmployees = Employee::where('company_id', $companyId)->where('status', '1')->count();

        $verifiedEmployees = Employee::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('aadhar_verify', 'Yes')
                  ->orWhere('pan_verify', 'Yes')
                  ->orWhere('voter_verify', 'Yes')
                  ->orWhere('dl_verify', 'Yes');
            })->count();

        $unverifiedEmployees = max(0, $totalEmployees - $verifiedEmployees);

        return view('employee.management.employees', compact(
            'employee',
            'employees',
            'departments',
            'designations',
            'branches',
            'shifts',
            'totalEmployees',
            'activeEmployees',
            'verifiedEmployees',
            'unverifiedEmployees'
        ));
    }

    /**
     * Store new Employee (employees.create)
     */
    public function storeEmployee(Request $request)
    {
        $employee = $this->authorizePermission('employees.create');
        $companyId = $employee->company_id;

        $request->validate([
            'name' => 'required|string|min:3|max:55',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|digits:10|unique:employees,phone',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'attendance_type' => 'nullable|string|in:geo,selfie,qr code,qr',
        ]);

        $position = $request->position;
        if ($request->filled('designation_id')) {
            $desig = Designation::where('company_id', $companyId)->find($request->designation_id);
            if ($desig) {
                $position = $desig->name;
            }
        }

        Employee::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'branch_id' => $request->branch_id ?: null,
            'department_id' => $request->department_id ?: null,
            'designation_id' => $request->designation_id ?: null,
            'shift_id' => $request->shift_id ?: null,
            'position' => $position,
            'salary' => $request->salary ?: 0,
            'attendance_type' => $request->attendance_type ?: 'geo',
            'status' => 1,
        ]);

        $company = CompanyDetail::find($companyId);
        if ($company) {
            $company->increment('employee_added', 1);
        }

        Alert::success('Success', 'Employee has been created successfully.');
        return redirect()->route('employee.management.employees');
    }

    /**
     * Update Employee (employees.edit)
     */
    public function updateEmployee(Request $request, $id)
    {
        $authEmp = $this->authorizePermission('employees.edit');
        $companyId = $authEmp->company_id;

        $targetEmp = Employee::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|min:3|max:55',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|digits:10|unique:employees,phone,' . $id,
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'status' => 'required',
            'attendance_type' => 'nullable|string|in:geo,selfie,qr code,qr',
        ]);

        $position = $request->position;
        if ($request->filled('designation_id')) {
            $desig = Designation::where('company_id', $companyId)->find($request->designation_id);
            if ($desig) {
                $position = $desig->name;
            }
        }

        $targetEmp->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'branch_id' => $request->branch_id ?: null,
            'department_id' => $request->department_id ?: null,
            'designation_id' => $request->designation_id ?: null,
            'shift_id' => $request->shift_id ?: null,
            'position' => $position,
            'salary' => $request->salary ?: 0,
            'attendance_type' => $request->attendance_type ?: 'geo',
            'status' => in_array($request->status, ['1', 1, 'active']) ? 1 : 0,
        ]);

        Alert::success('Success', 'Employee details updated successfully.');
        return redirect()->route('employee.management.employees');
    }

    /**
     * Delete Employee (employees.delete)
     */
    public function destroyEmployee($id)
    {
        $authEmp = $this->authorizePermission('employees.delete');
        $companyId = $authEmp->company_id;

        $targetEmp = Employee::where('company_id', $companyId)->findOrFail($id);
        $targetEmp->delete();

        Alert::success('Success', 'Employee deleted successfully.');
        return redirect()->route('employee.management.employees');
    }

    /**
     * Export Employees to Excel (employees.view)
     */
    public function exportEmployees(Request $request)
    {
        $authEmp = $this->authorizePermission('employees.view');
        $companyId = $authEmp->company_id;

        $name = $request->input('name') ?: $request->input('search');
        $branchId = $request->input('branch_id');
        $departmentId = $request->input('department_id');

        $employeesQuery = Employee::where('company_id', $companyId)
            ->with(['branch', 'department', 'designation', 'shift']);

        if ($name) {
            $employeesQuery->where(function ($q) use ($name) {
                $q->where('name', 'like', '%' . $name . '%')
                  ->orWhere('email', 'like', '%' . $name . '%')
                  ->orWhere('phone', 'like', '%' . $name . '%');
            });
        }

        if ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'A1' => 'Employee ID',
            'B1' => 'Name',
            'C1' => 'Email',
            'D1' => 'Phone',
            'E1' => 'Branch',
            'F1' => 'Department',
            'G1' => 'Designation',
            'H1' => 'Shift',
            'I1' => 'Salary',
            'J1' => 'Status',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $row = 2;
        foreach ($employees as $emp) {
            $sheet->setCellValue('A' . $row, $emp->emp_id ?? ('EMP-' . str_pad($emp->id, 4, '0', STR_PAD_LEFT)));
            $sheet->setCellValue('B' . $row, $emp->name);
            $sheet->setCellValue('C' . $row, $emp->email);
            $sheet->setCellValue('D' . $row, $emp->phone);
            $sheet->setCellValue('E' . $row, $emp->branch ? $emp->branch->branch_name : 'N/A');
            $sheet->setCellValue('F' . $row, $emp->department ? $emp->department->name : 'N/A');
            $sheet->setCellValue('G' . $row, $emp->designation ? $emp->designation->name : ($emp->position ?? 'N/A'));
            $sheet->setCellValue('H' . $row, $emp->shift ? $emp->shift->shift_name : 'N/A');
            $sheet->setCellValue('I' . $row, $emp->salary);
            $sheet->setCellValue('J' . $row, $emp->status == 1 ? 'Active' : 'Inactive');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'employees_management_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Import Employees from Excel (employees.create)
     */
    public function importEmployees(Request $request)
    {
        $authEmp = $this->authorizePermission('employees.create');
        $companyId = $authEmp->company_id;

        $request->validate([
            'attendance_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('attendance_file');
            $import = new EmployeeImport;
            $import->import($file, $companyId);

            if ($import->failureOccurred()) {
                Alert::error('Error', 'There was an issue while importing employee data.');
            } else {
                Alert::success('Success', 'Employees imported successfully.');
            }

            return redirect()->route('employee.management.employees');
        } catch (\Exception $e) {
            Alert::error('Error', 'Import failed: ' . $e->getMessage());
            return redirect()->route('employee.management.employees');
        }
    }

    /**
     * View Company Staff Leave Requests (Matches Company Panel)
     */
    public function leaves(Request $request)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || (!$employee->hasPermission('leaves.view_all') && !$employee->hasPermission('leaves.approve'))) {
            abort(403, 'Unauthorized. Your designation does not have permission to review leaves.');
        }

        $companyId = $employee->company_id;
        $employeeId = $request->get('employee_id');
        $leaveType = $request->get('leave_type');
        $status = $request->get('status');

        $query = EmployeeLeave::with(['employeeBasicInfo', 'leavetype'])
            ->where('company_id', $companyId)
            ->when($leaveType, fn($q) => $q->where('leave_type', $leaveType))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId));

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $employees = Employee::where('company_id', $companyId)->orderBy('name', 'asc')->get();

        $leaveCount = [];
        if (!empty($employeeId)) {
            $fromdate = date('Y') . '-01-01';
            $todate = date('Y') . '-12-31';
            $leaveCount = EmployeeLeave::where('employee_id', $employeeId)
                ->where('company_id', $companyId)
                ->select('leave_type', DB::raw('SUM(days) as total_days'))
                ->whereBetween('from_date', [$fromdate, $todate])
                ->groupBy('leave_type')
                ->get();
        }

        return view('employee.management.leaves', compact(
            'employee',
            'leaves',
            'employees',
            'leaveCount'
        ));
    }

    /**
     * Process Leave Request (Approve / Reject / Pending)
     */
    public function updateLeaveStatus(Request $request)
    {
        $employee = $this->authorizePermission('leaves.approve');
        $companyId = $employee->company_id;

        $request->validate([
            'leave_id' => 'required|exists:employee_leaves,id',
            'action' => 'required|in:approve,reject,pending',
        ]);

        $leave = EmployeeLeave::where('company_id', $companyId)->findOrFail($request->leave_id);

        if ($request->action === 'approve') {
            $leave->status = 'approved';
        } elseif ($request->action === 'reject') {
            $leave->status = 'rejected';
        } else {
            $leave->status = 'pending';
        }

        $leave->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave status updated successfully to ' . ucfirst($leave->status),
                'new_status' => $leave->status,
            ]);
        }

        Alert::success('Success', 'Leave status updated successfully to ' . ucfirst($leave->status));
        return redirect()->back();
    }

    /**
     * Delete Leave Application
     */
    public function deleteLeave($id)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || (!$employee->hasPermission('leaves.delete') && !$employee->hasPermission('leaves.approve'))) {
            abort(403, 'Unauthorized. Your designation does not have permission to delete leave applications.');
        }
        $companyId = $employee->company_id;

        $leave = EmployeeLeave::where('company_id', $companyId)->findOrFail($id);
        $leave->delete();

        Alert::success('Success', 'Leave application deleted successfully.');
        return redirect()->route('employee.management.leaves');
    }

    /**
     * View Company Staff Attendance Log (Matches Company Panel)
     */
    public function attendance(Request $request)
    {
        $employee = $this->authorizePermission('attendance.view_all');
        $companyId = $employee->company_id;

        $employeeId = $request->get('employee_id');
        $attendanceStatus = $request->get('attendance');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $attendances = Attendance::with(['employee', 'company', 'branch', 'department'])
            ->where('company_id', $companyId)
            ->when($employeeId, fn($query) => $query->where('employee_id', $employeeId))
            ->when($attendanceStatus, fn($query) => $query->where('attendance', $attendanceStatus))
            ->when($fromDate && $toDate, fn($q) => $q->whereBetween('date', [$fromDate, $toDate]))
            ->orderBy('date', 'desc')
            ->paginate(10);

        // Fetch company employees for filter dropdown
        $employees = Employee::where('company_id', $companyId)->orderBy('name', 'asc')->get();

        return view('employee.management.attendance', compact(
            'employee',
            'attendances',
            'employees'
        ));
    }

    /**
     * Edit attendance record (guarded by attendance.edit)
     */
    public function editAttendance($id)
    {
        $employee = $this->authorizePermission('attendance.edit');
        $companyId = $employee->company_id;

        $attendance = Attendance::where('company_id', $companyId)->findOrFail($id);
        $branches = Branch::where('company_id', $companyId)->where('status', 1)->get();
        $departments = Department::where('company_id', $companyId)->where('status', 1)->get();
        $employees = Employee::where('company_id', $companyId)->get();

        return view('employee.management.attendance_edit', compact('attendance', 'branches', 'departments', 'employees', 'employee'));
    }

    /**
     * Update attendance record (guarded by attendance.edit)
     */
    public function updateAttendance(Request $request, $id)
    {
        $employee = $this->authorizePermission('attendance.edit');
        $companyId = $employee->company_id;

        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'employee_id' => 'required|exists:employees,id',
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|in:Present,Absent,Leave',
            'date' => 'required|date',
            'in_time' => 'nullable',
            'out_time' => 'nullable',
            'halfday' => 'nullable|boolean',
        ]);

        $attendance = Attendance::where('company_id', $companyId)->findOrFail($id);
        $attendance->update([
            'branch_id' => $validatedData['branch_id'],
            'employee_id' => $validatedData['employee_id'],
            'department_id' => $validatedData['department_id'],
            'attendance' => $validatedData['attendance'],
            'date' => Carbon::parse($validatedData['date'])->format('Y-m-d'),
            'in_time' => $validatedData['in_time'] ?? null,
            'out_time' => $validatedData['out_time'] ?? null,
            'halfday' => $request->has('halfday') ? 1 : 0,
        ]);

        Alert::success('Success', 'Attendance updated successfully.');
        return redirect()->route('employee.management.attendance');
    }

    /**
     * Delete attendance record (guarded by attendance.delete or attendance.edit)
     */
    public function destroyAttendance($id)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || (!$employee->hasPermission('attendance.delete') && !$employee->hasPermission('attendance.edit'))) {
            abort(403, 'Unauthorized. Your designation does not have permission to delete attendance records.');
        }
        $companyId = $employee->company_id;

        $attendance = Attendance::where('company_id', $companyId)->findOrFail($id);
        $attendance->delete();

        Alert::success('Success', 'Attendance deleted successfully.');
        return redirect()->route('employee.management.attendance');
    }

    /**
     * View and Assign Team Tasks
     */
    public function tasks(Request $request)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || (!$employee->hasPermission('tasks.view') && !$employee->hasPermission('tasks.create') && !$employee->hasPermission('tasks.manage'))) {
            abort(403, 'Unauthorized. Your designation does not have permission to manage team tasks.');
        }

        $companyId = $employee->company_id;
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Task::where('company_id', $companyId)->with('assignedEmployees');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%");
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Company employees for assigning new tasks
        $assignableEmployees = Employee::where('company_id', $companyId)->where('status', '1')->orderBy('name', 'asc')->get();

        $totalTasks = Task::where('company_id', $companyId)->count();
        $pendingTasks = Task::where('company_id', $companyId)->where('status', 'pending')->count();
        $completedTasks = Task::where('company_id', $companyId)->where('status', 'completed')->count();

        return view('employee.management.tasks', compact(
            'employee',
            'tasks',
            'assignableEmployees',
            'totalTasks',
            'pendingTasks',
            'completedTasks',
            'status'
        ));
    }

    /**
     * Create and Assign Team Task
     */
    public function storeTask(Request $request)
    {
        $employee = $this->authorizePermission('tasks.create');
        $companyId = $employee->company_id;

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $task = Task::create([
            'company_id' => $companyId,
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date ?? date('Y-m-d'),
            'end_date' => $request->end_date ?? date('Y-m-d', strtotime('+3 days')),
            'priority' => $request->priority,
            'status' => 'pending',
        ]);

        foreach ($request->employee_ids as $empId) {
            TaskAssign::create([
                'task_id' => $task->id,
                'employee_id' => $empId,
            ]);
        }

        return redirect()->route('employee.management.tasks')->with('success', 'Task assigned successfully to team member(s).');
    }

    /**
     * View Company Staff Payroll & Payslips
     */
    public function payroll(Request $request)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || (!$employee->hasPermission('payroll.view') && !$employee->hasPermission('payroll.view_all') && !$employee->hasPermission('payroll.generate'))) {
            abort(403, 'Unauthorized. Your designation does not have permission to view company payroll.');
        }

        $companyId = $employee->company_id;
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));
        $search = $request->input('search');

        $query = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', (int)$month)
            ->where('salary_year', (int)$year);

        if (!empty($search)) {
            $query->where('employee_name', 'like', "%{$search}%");
        }

        $salarySummaries = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $totalPayout = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', (int)$month)
            ->where('salary_year', (int)$year)
            ->sum('net_salary');

        $processedCount = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', (int)$month)
            ->where('salary_year', (int)$year)
            ->count();

        return view('employee.management.payroll', compact(
            'employee',
            'salarySummaries',
            'totalPayout',
            'processedCount',
            'month',
            'year'
        ));
    }

    /**
     * View Company Branches (Branch)
     */
     public function branches(Request $request)
     {
         $employee = $this->authorizePermission('branches.view');
         $companyId = $employee->company_id;

         $branches = Branch::where('company_id', $companyId)
             ->withCount('employees')
             ->orderBy('created_at', 'desc')
             ->paginate(10);

         return view('employee.management.branches', compact('employee', 'branches'));
     }

    /**
     * View Company Work Shifts (Shift)
     */
     public function shifts(Request $request)
     {
         $employee = $this->authorizePermission('shifts.view');
         $companyId = $employee->company_id;

         $query = Shift::where('company_id', $companyId);
         if ($request->filled('shift_name')) {
             $query->where('shift_name', 'like', '%' . $request->shift_name . '%');
         }
         $shifts = $query->orderBy('created_at', 'desc')->paginate(10);

         return view('employee.management.shifts', compact('employee', 'shifts'));
     }

    /**
     * View Company Departments (Department)
     */
    public function departments(Request $request)
    {
        $employee = $this->authorizePermission('departments.view');
        $companyId = $employee->company_id;

        $departments = Department::where('company_id', $companyId)
            ->withCount('employees')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.management.departments', compact('employee', 'departments'));
    }

    /**
     * View Company Designations (Designation)
     */
    public function designations(Request $request)
    {
        $employee = $this->authorizePermission('designations.view');
        $companyId = $employee->company_id;

        $designations = Designation::where('company_id', $companyId)
            ->withCount('employees')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.management.designations', compact('employee', 'designations'));
    }

    /**
     * View HRMS Analytics & Reports
     */
    public function reports(Request $request)
    {
        $employee = $this->authorizePermission('reports.view');
        $companyId = $employee->company_id;

        $departments = Department::where('company_id', $companyId)->get();
        $branches = Branch::where('company_id', $companyId)->get();

        return view('employee.management.reports', compact(
            'employee',
            'departments',
            'branches'
        ));
    }

    /**
     * Export Employee Report (guarded by reports.view)
     */
    public function exportEmployeeReport(Request $request)
    {
        $employee = $this->authorizePermission('reports.view');
        $companyId = $employee->company_id;

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;

        if ($format === 'excel') {
            $export = new EmployeeExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } elseif ($format === 'pdf') {
            $export = new EmployeePDFExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }

    /**
     * Export Attendance Report (guarded by reports.view)
     */
    public function exportAttendanceReport(Request $request)
    {
        $employee = $this->authorizePermission('reports.view');
        $companyId = $employee->company_id;

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;

        if ($format === 'excel') {
            $export = new AttendanceExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } elseif ($format === 'pdf') {
            $export = new AttendancePDFExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }

    /**
     * Export Leave Report (guarded by reports.view)
     */
    public function exportLeaveReport(Request $request)
    {
        $employee = $this->authorizePermission('reports.view');
        $companyId = $employee->company_id;

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;

        if ($format === 'excel') {
            $export = new LeaveExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } elseif ($format === 'pdf') {
            $export = new LeavePDFExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }

    /**
     * Store new Branch (branches.create)
     */
    public function storeBranch(Request $request)
    {
        $employee = $this->authorizePermission('branches.create');
        $companyId = $employee->company_id;

        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $status = in_array($request->status, ['active', '1', 1]) ? 1 : 0;
        Branch::create([
            'company_id' => $companyId,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'status' => $status,
        ]);

        Alert::success('Success', 'Branch created successfully.');
        return redirect()->route('employee.management.branches');
    }

    /**
     * Update existing Branch (branches.edit)
     */
    public function updateBranch(Request $request, $id)
    {
        $employee = $this->authorizePermission('branches.edit');
        $companyId = $employee->company_id;

        $branch = Branch::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $status = in_array($request->status, ['active', '1', 1]) ? 1 : 0;
        $branch->update([
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'status' => $status,
        ]);

        Alert::success('Success', 'Branch updated successfully.');
        return redirect()->route('employee.management.branches');
    }

    /**
     * Delete Branch (branches.delete)
     */
    public function destroyBranch($id)
    {
        $employee = $this->authorizePermission('branches.delete');
        $companyId = $employee->company_id;

        $branch = Branch::where('company_id', $companyId)->findOrFail($id);
        $branch->delete();

        Alert::success('Success', 'Branch deleted successfully.');
        return redirect()->route('employee.management.branches');
    }

    /**
     * Store new Department (departments.create)
     */
    public function storeDepartment(Request $request)
    {
        $employee = $this->authorizePermission('departments.create');
        $companyId = $employee->company_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        Department::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'description' => $request->description,
            'status' => (int)$request->status,
        ]);

        Alert::success('Success', 'Department added successfully.');
        return redirect()->route('employee.management.departments');
    }

    /**
     * Update Department (departments.edit)
     */
    public function updateDepartment(Request $request, $id)
    {
        $employee = $this->authorizePermission('departments.edit');
        $companyId = $employee->company_id;

        $department = Department::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => (int)$request->status,
        ]);

        Alert::success('Success', 'Department updated successfully.');
        return redirect()->route('employee.management.departments');
    }

    /**
     * Delete Department (departments.delete)
     */
    public function destroyDepartment($id)
    {
        $employee = $this->authorizePermission('departments.delete');
        $companyId = $employee->company_id;

        $department = Department::where('company_id', $companyId)->findOrFail($id);
        $department->delete();

        Alert::success('Success', 'Department deleted successfully.');
        return redirect()->route('employee.management.departments');
    }

    /**
     * Store new Designation (designations.create)
     */
    public function storeDesignation(Request $request)
    {
        $employee = $this->authorizePermission('designations.create');
        $companyId = $employee->company_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Designation::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 1,
        ]);

        Alert::success('Success', 'Designation created successfully.');
        return redirect()->route('employee.management.designations');
    }

    /**
     * Update Designation (designations.edit)
     */
    public function updateDesignation(Request $request, $id)
    {
        $employee = $this->authorizePermission('designations.edit');
        $companyId = $employee->company_id;

        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $designation->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Alert::success('Success', 'Designation updated successfully.');
        return redirect()->route('employee.management.designations');
    }

    /**
     * Delete Designation (designations.delete)
     */
    public function destroyDesignation($id)
    {
        $employee = $this->authorizePermission('designations.delete');
        $companyId = $employee->company_id;

        $designation = Designation::where('company_id', $companyId)->findOrFail($id);
        Employee::where('designation_id', $designation->id)->update(['designation_id' => null, 'position' => null]);
        $designation->delete();

        Alert::success('Success', 'Designation deleted successfully.');
        return redirect()->route('employee.management.designations');
    }

    /**
     * Store new Shift (shifts.create)
     */
    public function storeShift(Request $request)
    {
        $employee = $this->authorizePermission('shifts.create');
        $companyId = $employee->company_id;

        $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'grace_time' => 'nullable|integer',
        ]);

        Shift::create([
            'company_id' => $companyId,
            'shift_name' => $request->shift_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'grace_time' => $request->grace_time ?? 0,
            'status' => 1,
        ]);

        Alert::success('Success', 'Shift created successfully.');
        return redirect()->route('employee.management.shifts');
    }

    /**
     * Update Shift (shifts.edit)
     */
    public function updateShift(Request $request, $id)
    {
        $employee = $this->authorizePermission('shifts.edit');
        $companyId = $employee->company_id;

        $shift = Shift::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'grace_time' => 'nullable|integer',
        ]);

        $shift->update([
            'shift_name' => $request->shift_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'grace_time' => $request->grace_time ?? 0,
        ]);

        Alert::success('Success', 'Shift updated successfully.');
        return redirect()->route('employee.management.shifts');
    }

    /**
     * Delete Shift (shifts.delete)
     */
    public function destroyShift($id)
    {
        $employee = $this->authorizePermission('shifts.delete');
        $companyId = $employee->company_id;

        $shift = Shift::where('company_id', $companyId)->findOrFail($id);
        $shift->delete();

        Alert::success('Success', 'Shift deleted successfully.');
        return redirect()->route('employee.management.shifts');
    }

    /**
     * Mark / Insert Attendance (attendance.create)
     */
    public function storeAttendance(Request $request)
    {
        $employee = $this->authorizePermission('attendance.create');
        $companyId = $employee->company_id;

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'attendance' => 'required|in:Present,Absent,Leave,0,1,2',
            'in_time' => 'nullable',
            'out_time' => 'nullable',
        ]);

        $targetEmp = Employee::where('company_id', $companyId)->findOrFail($request->employee_id);

        $statusStr = $request->attendance;
        if ($statusStr === '1' || $statusStr === 1) {
            $statusStr = 'Present';
        } elseif ($statusStr === '0' || $statusStr === 0) {
            $statusStr = 'Absent';
        } elseif ($statusStr === '2' || $statusStr === 2) {
            $statusStr = 'Leave';
        }

        Attendance::updateOrCreate(
            [
                'employee_id' => $targetEmp->id,
                'company_id' => $companyId,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
            ],
            [
                'branch_id' => $targetEmp->branch_id,
                'department_id' => $targetEmp->department_id,
                'attendance' => $statusStr,
                'in_time' => $request->in_time ? date('H:i:s', strtotime($request->in_time)) : null,
                'out_time' => $request->out_time ? date('H:i:s', strtotime($request->out_time)) : null,
            ]
        );

        Alert::success('Success', 'Attendance record marked successfully.');
        return redirect()->route('employee.management.attendance');
    }

    /**
     * Update Team Task (tasks.edit)
     */
    public function updateTask(Request $request, $id)
    {
        $employee = $this->authorizePermission('tasks.edit');
        $companyId = $employee->company_id;

        $task = Task::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        Alert::success('Success', 'Task updated successfully.');
        return redirect()->route('employee.management.tasks');
    }

    /**
     * Delete Team Task (tasks.delete)
     */
    public function destroyTask($id)
    {
        $employee = $this->authorizePermission('tasks.delete');
        $companyId = $employee->company_id;

        $task = Task::where('company_id', $companyId)->findOrFail($id);
        TaskAssign::where('task_id', $task->id)->delete();
        $task->delete();

        Alert::success('Success', 'Task deleted successfully.');
        return redirect()->route('employee.management.tasks');
    }
}
