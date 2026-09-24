<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Employee extends Authenticatable
{
    use HasFactory, HasApiTokens;

    // protected $dates = ['created_at', 'updated_at'];

    protected $dates = [
        'date_of_birth',
        'date_of_joining',
        'date_of_leaving', // add other dates that need to be formatted
    ];

    // Specify the fillable fields for mass assignment
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'salary',
        'ctc',
        'tax_regime',
        'tax_declarations',
        'company_id',
        'branch_id',
        'department_id',
        'date_of_birth',
        'gender',
        'marital_status',
        'blood_group',
        'guardian_name',
        'country',
        'state',
        'city',
        'address',
        'pin',
        'date_of_joining',
        'date_of_leaving',
        'job_title_id',
        'employee_type_id',
        'official_email_id',
        'esi_number',
        'pf_number',
        'privileged_leave',
        'sick_leave',
        'casual_leave',
        'image',
        'resume',
        'geo_status',
        'status',
        'device_status',
        'shift_id',
        'device_id',
        'fcm_token',
        'device_name',
        'android_version',
        'attendance_type',
        'designation_id',
        'company_role_id',
    ];
    // Auto-generate emp_id when creating new records
    // app/Models/Employee.php
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->emp_id = 'EMP-' . str_pad(static::max('id') + 1, 5, '0', STR_PAD_LEFT);
        });

        static::deleting(function (Employee $employee) {
            $empId = $employee->id;

            // 1. Delete physical files associated with the employee
            // Personal files
            if (!empty($employee->image)) {
                $imgPath = public_path('uploads/employees/' . $employee->image);
                if (file_exists($imgPath)) { @unlink($imgPath); }
            }
            if (!empty($employee->resume)) {
                $resumePaths = [
                    public_path('uploads/resumes/' . $employee->resume),
                    public_path('uploads/employees/' . $employee->resume),
                ];
                foreach ($resumePaths as $rp) {
                    if (file_exists($rp)) { @unlink($rp); }
                }
            }
            if (!empty($employee->selfie_image)) {
                $selfiePath = public_path('uploads/employees/selfie/' . $employee->selfie_image);
                if (file_exists($selfiePath)) { @unlink($selfiePath); }
            }

            // Employee documents files
            try {
                $documents = DB::table('employee_documents')->where('employee_id', $empId)->get();
                foreach ($documents as $doc) {
                    if (!empty($doc->file_path)) {
                        $docPath = public_path('uploads/employee_documents/' . $doc->file_path);
                        if (file_exists($docPath)) { @unlink($docPath); }
                    }
                }
            } catch (\Throwable $e) {}

            // Attendances punch images
            try {
                $attendances = DB::table('attendances')->where('employee_id', $empId)->get();
                foreach ($attendances as $att) {
                    if (!empty($att->punchin_image)) {
                        $pIn = public_path('uploads/employees/punchin/' . $att->punchin_image);
                        if (file_exists($pIn)) { @unlink($pIn); }
                    }
                    if (!empty($att->punchout_image)) {
                        $pOut = public_path('uploads/employees/punchout/' . $att->punchout_image);
                        if (file_exists($pOut)) { @unlink($pOut); }
                    }
                }
            } catch (\Throwable $e) {}

            // Reimbursements receipts
            try {
                $reimbursements = DB::table('reimbursements')->where('employee_id', $empId)->get();
                foreach ($reimbursements as $reimb) {
                    if (!empty($reimb->receipt_file)) {
                        $rPath = public_path('uploads/receipt/' . $reimb->receipt_file);
                        if (file_exists($rPath)) { @unlink($rPath); }
                    }
                }
            } catch (\Throwable $e) {}

            // Compoff leave receipts
            try {
                $compoffs = DB::table('compoff_leaves')->where('employee_id', $empId)->get();
                foreach ($compoffs as $comp) {
                    if (!empty($comp->receipt_file)) {
                        $cPaths = [
                            public_path('uploads/compoff/' . $comp->receipt_file),
                            public_path('uploads/receipt/' . $comp->receipt_file),
                        ];
                        foreach ($cPaths as $cp) {
                            if (file_exists($cp)) { @unlink($cp); }
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // Expenses receipts
            try {
                $expenses = DB::table('expenses')->where('employee_id', $empId)->get();
                foreach ($expenses as $exp) {
                    if (!empty($exp->bill_receipt)) {
                        $ePaths = [
                            public_path('uploads/employee_expenses/' . $exp->bill_receipt),
                            public_path('uploads/bill_receipt/' . $exp->bill_receipt),
                            public_path('uploads/expense_attachments/' . $exp->bill_receipt),
                        ];
                        foreach ($ePaths as $ep) {
                            if (file_exists($ep)) { @unlink($ep); }
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // 2. Trips and Driver cleanup
            try {
                $trips = DB::table('trips')
                    ->where('driver_id', $empId)
                    ->orWhere('employee_id', $empId)
                    ->pluck('id')
                    ->toArray();

                if (!empty($trips)) {
                    // Delete trip logs images
                    $tripLogs = DB::table('trip_logs')->whereIn('trip_id', $trips)->get();
                    foreach ($tripLogs as $tl) {
                        if (!empty($tl->image_path)) {
                            $tlPath = public_path($tl->image_path);
                            if (file_exists($tlPath)) { @unlink($tlPath); }
                        }
                    }
                    // Delete trip expenses bill receipts
                    $tripExpenses = DB::table('trip_expenses')->whereIn('trip_id', $trips)->get();
                    foreach ($tripExpenses as $te) {
                        if (!empty($te->bill_receipt)) {
                            $tePath = public_path('uploads/bill_receipt/' . $te->bill_receipt);
                            if (file_exists($tePath)) { @unlink($tePath); }
                        }
                    }

                    DB::table('trip_expenses')->whereIn('trip_id', $trips)->delete();
                    DB::table('trip_geo_locations')->whereIn('trip_id', $trips)->delete();
                    DB::table('trip_logs')->whereIn('trip_id', $trips)->delete();
                    DB::table('customers')->whereIn('trip_id', $trips)->delete();
                    DB::table('trips')->whereIn('id', $trips)->delete();
                }

                // Standalone driver/employee records in trip tables
                DB::table('trip_expenses')->where('driver_id', $empId)->delete();
                DB::table('trip_geo_locations')->where('driver_id', $empId)->delete();
                DB::table('trip_logs')->where('driver_id', $empId)->delete();
                DB::table('customers')->where('employee_id', $empId)->delete();
                DB::table('trips')->where('driver_id', $empId)->orWhere('employee_id', $empId)->delete();

                // Unassign driver from vehicles (keep company vehicle intact)
                DB::table('vehicles')->where('employee_id', $empId)->update(['employee_id' => null]);
            } catch (\Throwable $e) {}

            // 3. Delete database records in all related employee tables
            $tables = [
                'attendances',
                'attendance_requests',
                'employee_punches',
                'employee_geo_locations',
                'employee_leaves',
                'compoff_leaves',
                'employee_salaries',
                'employee_salary_summaries',
                'salarytypes',
                'bank_accounts',
                'employee_documents',
                'employee_performances',
                'employee_permission_overrides',
                'task_assigns',
                'task_comments',
                'employee_shifts',
                'expenses',
                'reimbursements',
                'device_sessions',
                'device_logs',
                'feedback',
                'tickets',
                'ticket_replies',
                'notifications',
                'leads',
                'lead_followups',
            ];

            foreach ($tables as $tbl) {
                try {
                    DB::table($tbl)->where('employee_id', $empId)->delete();
                } catch (\Throwable $e) {}
            }

            // 4. Delete Sanctum API tokens
            try {
                if (method_exists($employee, 'tokens')) {
                    $employee->tokens()->delete();
                }
            } catch (\Throwable $e) {}

            // 5. Decrement company employee_added slot counter
            try {
                if (!empty($employee->company_id)) {
                    DB::table('company_details')
                        ->where('user_id', $employee->company_id)
                        ->where('employee_added', '>', 0)
                        ->decrement('employee_added', 1);
                }
            } catch (\Throwable $e) {}
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function companyRole()
    {
        return $this->belongsTo(CompanyRole::class, 'company_role_id');
    }

    public function permissionOverrides()
    {
        return $this->hasMany(EmployeePermissionOverride::class, 'employee_id');
    }

    /**
     * Check if employee has permission (evaluating designation role permissions and employee-specific overrides)
     */
    public function hasPermission($permissionKey)
    {
        // Resolve aliases for backward compatibility
        $checkKeys = [$permissionKey];
        $aliasMap = [
            'attendance.view_all' => ['attendance.view', 'attendance.view_all'],
            'attendance.view' => ['attendance.view_all', 'attendance.view'],
            'leaves.view_all' => ['leaves.view', 'leaves.view_all'],
            'leaves.view' => ['leaves.view_all', 'leaves.view'],
            'leaves.approve' => ['leaves.edit', 'leaves.approve'],
            'payroll.view_all' => ['payroll.view', 'payroll.view_all'],
            'payroll.view' => ['payroll.view_all', 'payroll.view'],
            'payroll.generate' => ['payroll.create', 'payroll.generate'],
            'departments.manage' => ['departments.view', 'departments.edit', 'departments.manage'],
            'branches.manage' => ['branches.view', 'branches.edit', 'branches.manage'],
            'shifts.manage' => ['shifts.view', 'shifts.edit', 'shifts.manage'],
            'tasks.manage' => ['tasks.edit', 'tasks.manage'],
        ];

        if (isset($aliasMap[$permissionKey])) {
            $checkKeys = $aliasMap[$permissionKey];
        }

        // 1. Check direct employee override if present
        foreach ($checkKeys as $k) {
            $override = $this->permissionOverrides->firstWhere('permission_key', $k);
            if ($override) {
                return (bool)$override->is_granted;
            }
        }

        // 2. Default basic self-service actions guaranteed to every employee (own profile, holiday calendar, self attendance punch & salary slips)
        if (in_array($permissionKey, [
            'attendance.punch',
            'attendance.self',
            'leaves.apply',
            'leaves.self',
            'payroll.self',
            'payroll.download',
            'salary_slips.view',
            'documents.view',
            'profile.view',
            'profile.edit',
            'holidays.view',
        ])) {
            return true;
        }

        // 3. Check assigned designation (Designation is the Role)
        if ($this->designation && $this->designation->status && $this->designation->permissions->isNotEmpty()) {
            foreach ($checkKeys as $k) {
                if ($this->designation->hasPermission($k)) {
                    return true;
                }
            }
        }

        // 4. Fallback to companyRole if assigned
        if ($this->companyRole && $this->companyRole->status && $this->companyRole->permissions->isNotEmpty()) {
            foreach ($checkKeys as $k) {
                if ($this->companyRole->hasPermission($k)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all effective permission keys for this employee
     */
    public function getEffectivePermissions()
    {
        // 1. Base standard self-service permissions
        $perms = [
            'attendance.view', 'attendance.punch',
            'leaves.view', 'leaves.apply',
            'tasks.view', 'tasks.update_status',
            'payroll.self', 'payroll.view', 'payroll.download',
            'documents.view',
            'holidays.view',
            'profile.view',
        ];

        // 2. Add Designation Role permissions
        if ($this->designation && $this->designation->status && $this->designation->permissions->isNotEmpty()) {
            $perms = array_merge($perms, $this->designation->permission_keys);
        } elseif ($this->companyRole && $this->companyRole->status && $this->companyRole->permissions->isNotEmpty()) {
            $perms = array_merge($perms, $this->companyRole->permission_keys);
        }

        // 3. Apply employee-specific overrides if any
        if ($this->permissionOverrides->isNotEmpty()) {
            foreach ($this->permissionOverrides as $override) {
                if ($override->is_granted) {
                    if (!in_array($override->permission_key, $perms)) {
                        $perms[] = $override->permission_key;
                    }
                } else {
                    $perms = array_diff($perms, [$override->permission_key]);
                }
            }
        }

        return array_values(array_unique($perms));
    }

    // Define the relationship with BankAccount
    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function document()
    {
        return $this->hasOne(EmployeeDocument::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }


    public function leaves()
    {
        return $this->hasMany(EmployeeLeave::class);
    }

    // In Employee model
    public function punches()
    {
        return $this->hasMany(EmployeePunch::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    // public function shifts()
    // {
    //     return $this->belongsToMany(Shift::class, 'employee_shifts', 'employee_id', 'shift_id', 'company_id');
    // }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_shifts', 'employee_id', 'shift_id')
            ->withPivot('company_id'); // Include the company_id in the pivot
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class); // Employee belongs to one shift
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country'); // Employee belongs to one Country
    }
    public function state()
    {
        return $this->belongsTo(State::class); // Employee belongs to one State
    }
    public function city()
    {
        return $this->belongsTo(City::class); // Employee belongs to one City
    }

    public function assignedTasks()
    {
        return $this->hasMany(TaskAssign::class, 'employee_id');
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assigns', 'employee_id', 'task_id');
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Get image url attribute
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(public_path('uploads/employees/' . $this->image))) {
            return asset('uploads/employees/' . $this->image);
        }
        return null;
    }

    /**
     * Retrieve all shifts assigned to this employee (pivot or legacy shift_id)
     */
    public function getAllShifts()
    {
        $shifts = $this->relationLoaded('shifts') ? $this->shifts : $this->shifts()->get();
        if ($shifts->isEmpty() && $this->shift_id) {
            $single = $this->relationLoaded('shift') ? $this->shift : $this->shift()->first();
            if ($single) {
                $shifts = collect([$single]);
            }
        }
        return $shifts;
    }

    /**
     * Check if employee is currently in their assigned shift window, or for a specific timestamp.
     * Returns:
     * - null: if employee has no shift assigned
     * - ['active' => true, 'shift' => ..., 'start' => ..., 'end' => ..., 'is_overnight' => ...]
     * - ['active' => false, 'shifts' => ...]
     */
    public function getActiveShiftWindow($checkTime = null)
    {
        $tz = config('app.timezone', 'Asia/Calcutta');
        $time = $checkTime ? Carbon::parse($checkTime, $tz) : Carbon::now($tz);

        $shifts = $this->getAllShifts();
        if ($shifts->isEmpty()) {
            return null; // No shifts configured
        }

        $today = $time->copy()->startOfDay();
        $yesterday = $today->copy()->subDay();

        foreach ([$today, $yesterday] as $dayDate) {
            $dayName = strtolower($dayDate->format('l'));

            foreach ($shifts as $shift) {
                $isWorkingDay = isset($shift->{$dayName}) ? ((int)$shift->{$dayName} === 1) : true;
                if (!$isWorkingDay) {
                    continue;
                }

                $startTimeStr = date('H:i:s', strtotime($shift->start_time));
                $endTimeStr = date('H:i:s', strtotime($shift->end_time));

                $shiftStart = Carbon::parse($dayDate->format('Y-m-d') . ' ' . $startTimeStr, $tz);
                $shiftEnd = Carbon::parse($dayDate->format('Y-m-d') . ' ' . $endTimeStr, $tz);

                if ($shiftEnd->lte($shiftStart)) {
                    $shiftEnd->addDay();
                }

                if ($time->betweenIncluded($shiftStart, $shiftEnd)) {
                    return [
                        'active' => true,
                        'shift' => $shift,
                        'start' => $shiftStart,
                        'end' => $shiftEnd,
                        'is_overnight' => $shiftEnd->format('Y-m-d') !== $shiftStart->format('Y-m-d'),
                    ];
                }
            }
        }

        return [
            'active' => false,
            'shifts' => $shifts,
        ];
    }

    /**
     * Get the shift window for a specific calendar date (e.g. for route history).
     */
    public function getShiftWindowForDate($date)
    {
        $tz = config('app.timezone', 'Asia/Calcutta');
        $dayDate = Carbon::parse($date, $tz)->startOfDay();
        $dayName = strtolower($dayDate->format('l'));

        $shifts = $this->getAllShifts();
        if ($shifts->isEmpty()) {
            return null;
        }

        // Find shift active on this day of week
        $activeShift = null;
        foreach ($shifts as $shift) {
            if (isset($shift->{$dayName}) && (int)$shift->{$dayName} === 1) {
                $activeShift = $shift;
                break;
            }
        }

        if (!$activeShift) {
            $activeShift = $shifts->first();
            $isOffDay = isset($activeShift->{$dayName}) && (int)$activeShift->{$dayName} === 0;
        } else {
            $isOffDay = false;
        }

        $startTimeStr = date('H:i:s', strtotime($activeShift->start_time));
        $endTimeStr = date('H:i:s', strtotime($activeShift->end_time));

        $shiftStart = Carbon::parse($dayDate->format('Y-m-d') . ' ' . $startTimeStr, $tz);
        $shiftEnd = Carbon::parse($dayDate->format('Y-m-d') . ' ' . $endTimeStr, $tz);

        if ($shiftEnd->lte($shiftStart)) {
            $shiftEnd->addDay();
        }

        return [
            'shift' => $activeShift,
            'start' => $shiftStart,
            'end' => $shiftEnd,
            'is_off_day' => $isOffDay,
            'is_overnight' => $shiftEnd->format('Y-m-d') !== $shiftStart->format('Y-m-d'),
        ];
    }
}