<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salarytype extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'package_id',
        'department_id',
        'employee_id',
        'payment_type',
        'salary_type',
        'salary_type_description',
        'amount',
        'amount_type',
        'status',
        'working_days',
    ];

    public function package()
    {
        return $this->belongsTo(SalryTypePackage::class, 'package_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
