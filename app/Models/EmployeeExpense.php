<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeExpense extends Model
{
    use HasFactory;

      protected $fillable = [
        'company_id', 'employee_id', 'expense_type','amount','note','bill_receipt', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class);
    }
}
