<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
    ];
    // Auto-generate emp_id when creating new records
    // app/Models/Employee.php
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->emp_id = 'EMP-' . str_pad(static::max('id') + 1, 5, '0', STR_PAD_LEFT);
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
    // Define the relationship with BankAccount
    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function document()
    {
        return $this->hasOne(EmployeeDocument::class);
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
}