<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'expenseform_id',
        'expense_value',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function expenseFormDetails()
    {
        return $this->belongsTo(Expenseform::class, 'expenseform_id');
    }

}
