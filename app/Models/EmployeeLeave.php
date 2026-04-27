<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'branch_id',
        'department_id',
        'employee_id',
        'from_date',
        'to_date',
        'reason',
        'leave_type',
        'days',
        'status',

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function employeeBasicInfo()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->select('id', 'emp_id', 'name', 'email', 'phone', 'image', 'privileged_leave', 'sick_leave', 'casual_leave');
    }

    // Relationship with Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
    public function leavetype()
    {
        return $this->belongsTo(Leavetype::class, 'leave_type');
    }
}