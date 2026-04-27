<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'employee_id',
        'amount',
        'status',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
    
    public function expense_details()
    {
        return $this->hasMany(ExpenseDetail::class, 'expense_id');
    }
    public function attachments()
    {
        return $this->hasMany(ExpenseAttachment::class, 'expense_id')->select('id','expense_id','filename');
    }

    public function expense_type()
    {
        return $this->belongsTo(Expensetype::class, 'expensetype_id');
    }
}
