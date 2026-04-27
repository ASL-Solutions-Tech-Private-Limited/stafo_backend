<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'account_number',
        'bank_name',
        'branch_name',
        'ifsc_code',
        'account_holder_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}