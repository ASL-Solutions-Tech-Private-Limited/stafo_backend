<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalarySummary extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_salary_summaries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'employee_id',
        'employee_name',
        'salary_month',
        'salary_year',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'other_deduction',
        'reimbursement',
        'net_salary',
        'absent_days',
        'working_days',
        'total_working_days',
        'holiday_count',
        'present_days',
        'status',
        'generated_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_earning' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'reimbursement' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'absent_days' => 'integer',
        'working_days' => 'integer',
        'generated_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the salary summary.
     */
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class);
    }

    /**
     * Get the employee that owns the salary summary.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    // * Get the bank account associated with the employee through the salary summary.
    //  */
    public function bankAccount()
    {
        return $this->hasOneThrough(
            BankAccount::class,
            Employee::class,
            'id', // Foreign key on employees table
            'employee_id', // Foreign key on bank_accounts table
            'employee_id', // Local key on employee_salary_summaries table
            'id' // Local key on employees table
        );
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to filter by employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope a query to filter by salary month and year.
     */
    public function scopeForSalaryPeriod($query, $month, $year)
    {
        return $query->where('salary_month', $month)->where('salary_year', $year);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get the full salary period (Month Year).
     */
    public function getSalaryPeriodAttribute()
    {
        return $this->salary_month . ' ' . $this->salary_year;
    }

    /**
     * Calculate the total deductions including other deductions.
     */
    public function getTotalDeductionsWithOtherAttribute()
    {
        return $this->total_deduction + $this->other_deduction;
    }

    /**
     * Check if salary is already paid.
     */
    public function isPaid()
    {
        return $this->status === 'Paid';
    }

    /**
     * Check if salary is generated but not paid.
     */
    public function isGenerated()
    {
        return $this->status === 'Generated';
    }
}