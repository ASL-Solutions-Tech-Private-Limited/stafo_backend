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
        'department_name',
        'salary_month',
        'salary_year',
        'basic_salary',
        'total_earning',
        'total_deduction',
        'other_deduction',
        'reimbursement',
        'net_salary',
        'gross_salary',
        'absent_days',
        'working_days',
        'total_working_days',
        'holiday_count',
        'present_days',
        'leave_days',
        'late_count',
        'halfday_count',
        'pf_employee',
        'pf_employer',
        'esi_employee',
        'esi_employer',
        'pt_amount',
        'tds_amount',
        'arrears',
        'bonus',
        'ctc',
        'gratuity',
        'tax_regime',
        'sandwich_deduction',
        'status',
        'payment_status',
        'payment_mode',
        'paid_date',
        'is_locked',
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
        'arrears' => 'decimal:2',
        'bonus' => 'decimal:2',
        'ctc' => 'decimal:2',
        'gratuity' => 'decimal:2',
        'sandwich_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'pf_employee' => 'decimal:2',
        'pf_employer' => 'decimal:2',
        'esi_employee' => 'decimal:2',
        'esi_employer' => 'decimal:2',
        'pt_amount' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'absent_days' => 'decimal:1',
        'working_days' => 'integer',
        'total_working_days' => 'integer',
        'holiday_count' => 'integer',
        'present_days' => 'decimal:1',
        'leave_days' => 'decimal:1',
        'late_count' => 'integer',
        'halfday_count' => 'decimal:1',
        'is_locked' => 'boolean',
        'paid_date' => 'datetime',
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
     * Alias accessor for salary_month.
     */
    public function getMonthAttribute()
    {
        return $this->salary_month;
    }

    /**
     * Alias accessor for salary_year.
     */
    public function getYearAttribute()
    {
        return $this->salary_year;
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

    /**
     * Get Net Salary in Indian Currency Words.
     */
    public function getNetSalaryInWordsAttribute()
    {
        $amount = (float)($this->net_salary ?? 0);
        return self::convertNumberToWords($amount);
    }

    public static function convertNumberToWords($number)
    {
        $no = floor($number);
        $decimal = round(($number - $no) * 100);
        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
            30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];
        $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

        $str = [];
        $i = 0;
        while ($no > 0) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? '' : '';
                $unit = ($counter == 1) ? $digits[$counter] : ($counter ? $digits[$counter] : '');
                $str[] = ($number < 21) ? $words[$number] . ' ' . $unit :
                    $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $unit;
            } else {
                $str[] = '';
            }
        }
        $rupees = implode(' ', array_reverse($str));
        $rupees = trim(preg_replace('/\s+/', ' ', $rupees));
        $paise = ($decimal > 0) ? " and " . ($words[$decimal] ?? $decimal) . " Paise" : '';

        return $rupees ? "Rupees " . $rupees . $paise . " Only" : "Zero Rupees Only";
    }
}