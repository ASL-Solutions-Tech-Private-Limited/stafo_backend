<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'salary_month',
        'salary_year',
        'salary_type_id',
        'salary_type_amount',
        'salary_type_amount_type',
        'amount',
        'label',
        'payment_type',
        'basic_salary',
        'gross_salary',
        'other_deduction',
        'absent_days',
        'working_days',
        'reimbursement',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
    public function salarytype()
    {
        return $this->belongsTo(Salarytype::class, 'salary_type_id', 'id');
    }
}