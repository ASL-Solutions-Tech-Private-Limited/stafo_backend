<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'start_date',   
        'end_date',
        'status',
        'priority',
    ];


    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id')->select('id', 'company_name', 'company_code', 'email', 'mobile_no');
    }

    public function assignedEmployees()
    {
        return $this->belongsToMany(Employee::class, 'task_assigns', 'task_id', 'employee_id')->select('employees.id','emp_id','name','email','phone');
    }

    public function taskFiles()
    {
        return $this->hasMany(TaskImages::class, 'task_id')->select('id','task_id','filename');
    }
    
}
