<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\DesignationPermission;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CompanyRoleController extends Controller
{
    /**
     * Standard Management & Administrative Permissions Catalog for Companies.
     * Note: Basic employee self-service (punch in/out, view own profile, submit leaves,
     * download own payslips) is granted to all employees by default.
     * The permissions below define administrative management privileges for Designations/Roles.
     */
    public static function getPermissionsCatalog()
    {
        return [
            'branches' => [
                'name' => 'Branch',
                'icon' => 'fa-solid fa-building text-success',
                'permissions' => [
                    'branches.view' => 'View Branches',
                    'branches.create' => 'Add New Branch',
                    'branches.edit' => 'Edit Branch Info',
                    'branches.delete' => 'Delete Branch',
                ],
            ],
            'attendance' => [
                'name' => 'Emp Attendance',
                'icon' => 'fa-solid fa-clock text-success',
                'permissions' => [
                    'attendance.view' => 'View Staff Attendance',
                    'attendance.create' => 'Mark / Insert Attendance',
                    'attendance.edit' => 'Edit Punches & Adjustments',
                    'attendance.delete' => 'Delete Attendance Record',
                ],
            ],
            'shifts' => [
                'name' => 'Shift',
                'icon' => 'fa-solid fa-business-time text-primary',
                'permissions' => [
                    'shifts.view' => 'View Shift Schedules',
                    'shifts.create' => 'Create New Shift',
                    'shifts.edit' => 'Edit Shift Timings',
                    'shifts.delete' => 'Delete Shift',
                ],
            ],
            'employees' => [
                'name' => 'Employee',
                'icon' => 'fa-solid fa-users text-primary',
                'permissions' => [
                    'employees.view' => 'View Staff Directory',
                    'employees.create' => 'Add & Onboard Staff',
                    'employees.edit' => 'Edit Staff Profile & KYC',
                    'employees.delete' => 'Delete / Remove Staff',
                ],
            ],
            'departments' => [
                'name' => 'Department',
                'icon' => 'fa-solid fa-layer-group text-info',
                'permissions' => [
                    'departments.view' => 'View Department List',
                    'departments.create' => 'Add New Department',
                    'departments.edit' => 'Edit Department Details',
                    'departments.delete' => 'Delete Department',
                ],
            ],
            'designations' => [
                'name' => 'Designation',
                'icon' => 'fa-solid fa-id-card text-warning',
                'permissions' => [
                    'designations.view' => 'View Designation List',
                    'designations.create' => 'Add New Designation',
                    'designations.edit' => 'Edit Designation Details',
                    'designations.delete' => 'Delete Designation',
                ],
            ],
            'leaves' => [
                'name' => 'Leave',
                'icon' => 'fa-solid fa-calendar-check text-purple',
                'permissions' => [
                    'leaves.view' => 'View Staff Leave Requests',
                    'leaves.create' => 'Apply Leave on Behalf',
                    'leaves.edit' => 'Approve / Reject Leave Status',
                    'leaves.delete' => 'Delete Leave Application',
                ],
            ],
            'payroll' => [
                'name' => 'Staff payroll and Salary',
                'icon' => 'fa-solid fa-file-invoice-dollar text-warning',
                'permissions' => [
                    'payroll.view' => 'View Staff Payslips & Summaries',
                    'payroll.create' => 'Run & Compute Payroll',
                    'payroll.edit' => 'Adjust Salary & Deductions',
                    'payroll.delete' => 'Delete / Revert Payroll Record',
                ],
            ],
            'tasks' => [
                'name' => 'Team task',
                'icon' => 'fa-solid fa-list-check text-info',
                'permissions' => [
                    'tasks.view' => 'View Team Tasks',
                    'tasks.create' => 'Create & Assign Task',
                    'tasks.edit' => 'Edit Task Status & Details',
                    'tasks.delete' => 'Delete Task',
                ],
            ],
            'reports' => [
                'name' => 'Download Report',
                'icon' => 'fa-solid fa-chart-line text-danger',
                'permissions' => [
                    'reports.view' => 'View Reports Dashboard',
                    'reports.create' => 'Download & Export Reports',
                    'reports.edit' => 'Configure Report Parameters',
                    'reports.delete' => 'Delete Report Logs',
                ],
            ],
        ];
    }

    /**
     * Display unified Company Roles & Permissions Manager.
     * Note: Designation IS the Role.
     */
    public function index(Request $request)
    {
        if (Auth::user()->is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();

        // 1. Fetch all company Designations (which serve as Roles) with their permissions and assigned staff count
        $roles = Designation::where('company_id', $companyId)
            ->with(['permissions', 'employees'])
            ->withCount('employees')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Base Employee Query with Relations
        $employeeQuery = Employee::where('company_id', $companyId)
            ->with(['designation', 'department']);

        // Backend Search Filter (Name, EMP ID, Email)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $employeeQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('emp_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Backend Designation / Role Filter
        if ($request->filled('role_filter')) {
            $roleFilter = $request->role_filter;
            if ($roleFilter === '__none__') {
                $employeeQuery->whereNull('designation_id');
            } elseif (is_numeric($roleFilter)) {
                $employeeQuery->where('designation_id', $roleFilter);
            }
        }

        // Backend Pagination (10 per page, completely managed by backend)
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 10;
        }

        $employees = $employeeQuery->orderBy('name', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // If AJAX request: return table partial directly (Zero page refresh)
        if ($request->ajax()) {
            $html = view('user.roles.partials.employee_table', compact('employees', 'roles'))->render();
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'html' => $html,
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'total' => $employees->total(),
                ]);
            }
            return $html;
        }

        // 3. Permissions catalog
        $catalog = self::getPermissionsCatalog();
        $totalCatalogPerms = 0;
        foreach ($catalog as $cat) {
            $totalCatalogPerms += count($cat['permissions']);
        }

        // 4. Stats
        $totalRoles = $roles->count();
        $totalEmployeesCount = Employee::where('company_id', $companyId)->count();
        $assignedStaffCount = Employee::where('company_id', $companyId)->whereNotNull('designation_id')->count();
        $standardStaffCount = Employee::where('company_id', $companyId)->whereNull('designation_id')->count();

        // 5. Check if direct edit for a specific designation requested
        $autoOpenDesignationId = $request->query('designation_id');

        return view('user.roles.index', compact(
            'roles',
            'employees',
            'catalog',
            'totalCatalogPerms',
            'totalRoles',
            'totalEmployeesCount',
            'assignedStaffCount',
            'standardStaffCount',
            'autoOpenDesignationId'
        ));
    }

    /**
     * AJAX endpoint to fetch designation/role data for editing in modal.
     */
    public function getRoleData($id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)
            ->with('permissions')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'role' => [
                'id' => $designation->id,
                'name' => $designation->name,
                'description' => $designation->description,
                'status' => $designation->status,
                'permissions' => $designation->permission_keys,
            ],
        ]);
    }

    /**
     * Store a newly created designation (role) with permissions.
     */
    public function store(Request $request)
    {
        $companyId = Auth::id();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) use ($companyId) {
                    if (Designation::where('company_id', $companyId)->where('name', trim($value))->exists()) {
                        $fail('A designation/role with this name already exists in your company.');
                    }
                },
            ],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        DB::beginTransaction();
        try {
            $designation = Designation::create([
                'company_id' => $companyId,
                'name' => trim($request->name),
                'description' => $request->description ? trim($request->description) : null,
                'status' => 1,
            ]);

            if ($request->filled('permissions')) {
                foreach ($request->permissions as $permKey) {
                    DesignationPermission::create([
                        'designation_id' => $designation->id,
                        'permission_key' => $permKey,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => "Designation / Role '{$designation->name}' created successfully!",
                ]);
            }

            Alert::success('Role Created', "Designation '{$designation->name}' created with configured permissions!");
            return redirect()->route('company-roles.index')
                ->with('success', "Designation '{$designation->name}' created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing designation (role) and its permissions.
     */
    public function update(Request $request, $id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) use ($companyId, $id) {
                    if (Designation::where('company_id', $companyId)->where('name', trim($value))->where('id', '!=', $id)->exists()) {
                        $fail('Another designation with this name already exists.');
                    }
                },
            ],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        DB::beginTransaction();
        try {
            $designation->update([
                'name' => trim($request->name),
                'description' => $request->description ? trim($request->description) : null,
            ]);

            // Also keep employee position titles in sync if designation name changed
            Employee::where('designation_id', $designation->id)->update(['position' => $designation->name]);

            // Sync permissions in designation_permissions
            DesignationPermission::where('designation_id', $designation->id)->delete();

            if ($request->filled('permissions')) {
                foreach ($request->permissions as $permKey) {
                    DesignationPermission::create([
                        'designation_id' => $designation->id,
                        'permission_key' => $permKey,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => "Role '{$designation->name}' permissions updated successfully!",
                ]);
            }

            Alert::success('Updated', "Designation '{$designation->name}' permissions updated successfully!");
            return redirect()->route('company-roles.index')
                ->with('success', "Role '{$designation->name}' permissions updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Delete a designation/role and detach its employees.
     */
    public function destroy($id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);
        $roleName = $designation->name;

        DB::beginTransaction();
        try {
            // Unassign employees
            Employee::where('designation_id', $designation->id)->update([
                'designation_id' => null,
                'position' => null
            ]);

            DesignationPermission::where('designation_id', $designation->id)->delete();
            $designation->delete();

            DB::commit();

            Alert::success('Removed', "Designation '{$roleName}' has been deleted.");
            return redirect()->route('company-roles.index')
                ->with('success', "Designation '{$roleName}' deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting designation: ' . $e->getMessage());
        }
    }

    /**
     * Assign or unassign a designation (role) to an employee in 1 click.
     */
    public function assignEmployeeRole(Request $request)
    {
        $companyId = Auth::id();

        $request->validate([
            'employee_id' => [
                'required',
                function ($attribute, $value, $fail) use ($companyId) {
                    if (!Employee::where('company_id', $companyId)->where('id', $value)->exists()) {
                        $fail('Invalid employee selected.');
                    }
                },
            ],
            'designation_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($companyId) {
                    if ($value && !Designation::where('company_id', $companyId)->where('id', $value)->exists()) {
                        $fail('Invalid designation selected.');
                    }
                },
            ],
        ]);

        $employee = Employee::where('company_id', $companyId)->findOrFail($request->employee_id);
        $desigId = $request->filled('designation_id') ? $request->designation_id : null;

        $employee->designation_id = $desigId;
        if ($desigId) {
            $desig = Designation::find($desigId);
            $employee->position = $desig ? $desig->name : null;
            $roleName = $desig ? $desig->name : 'Staff';
        } else {
            $employee->position = null;
            $roleName = 'Standard Staff (No Designation)';
        }
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => "Assigned designation for {$employee->name} updated to {$roleName}.",
            'employee_name' => $employee->name,
            'role_name' => $roleName,
            'is_administrative' => (bool)$desigId,
        ]);
    }

    /**
     * Toggle status (active / inactive) for a designation.
     */
    public function toggleStatus($id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $designation->status = $designation->status ? 0 : 1;
        $designation->save();

        return response()->json([
            'status' => true,
            'new_status' => $designation->status,
            'message' => "Designation '{$designation->name}' is now " . ($designation->status ? 'Active' : 'Inactive') . '.',
        ]);
    }

    /**
     * Backward-compatibility fallbacks.
     */
    public function getEmployeeData(Request $request)
    {
        $companyId = Auth::id();
        if ($request->filled('employee_id')) {
            $employee = Employee::where('company_id', $companyId)->find($request->employee_id);
            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Employee not found'], 404);
            }
            return response()->json([
                'status' => true,
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'designation_id' => $employee->designation_id,
            ]);
        }
        return response()->json(['status' => false], 400);
    }

    public function savePermissions(Request $request)
    {
        return $this->assignEmployeeRole($request);
    }

    /**
     * Instant AJAX toggle: assign / unassign a single permission for a designation.
     */
    public function togglePermission(Request $request, $id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'permission_key' => 'required|string',
            'state' => 'required',
        ]);

        $permKey = trim($request->permission_key);
        $state = filter_var($request->state, FILTER_VALIDATE_BOOLEAN);

        if ($state) {
            DesignationPermission::firstOrCreate([
                'designation_id' => $designation->id,
                'permission_key' => $permKey,
            ]);
        } else {
            DesignationPermission::where('designation_id', $designation->id)
                ->where('permission_key', $permKey)
                ->delete();
        }

        $activeCount = DesignationPermission::where('designation_id', $designation->id)->count();

        return response()->json([
            'status' => true,
            'message' => $state ? "Permission assigned." : "Permission unassigned.",
            'permission_key' => $permKey,
            'state' => $state,
            'active_count' => $activeCount,
            'designation_id' => $designation->id,
        ]);
    }

    /**
     * Instant AJAX sync: update all permissions for a designation (for bulk/module toggle).
     */
    public function syncPermissions(Request $request, $id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $perms = $request->input('permissions', []);

        DB::beginTransaction();
        try {
            DesignationPermission::where('designation_id', $designation->id)->delete();

            foreach ($perms as $permKey) {
                DesignationPermission::create([
                    'designation_id' => $designation->id,
                    'permission_key' => $permKey,
                ]);
            }

            DB::commit();

            $activeCount = count($perms);

            return response()->json([
                'status' => true,
                'message' => 'Permissions synchronized successfully.',
                'active_count' => $activeCount,
                'designation_id' => $designation->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
