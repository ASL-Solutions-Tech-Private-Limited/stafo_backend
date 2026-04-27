<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'amount',
        'status',
        'date',
        'employee_id',
        'company_id',
        'receipt_file'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->select('id','emp_id', 'name','email','phone');
    }
}
