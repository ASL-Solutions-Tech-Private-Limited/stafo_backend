<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'lead_id',
        'type',
        'remarks',
        'status',  
        'next_date',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id')->select('id', 'company_name','company_code','email','mobile_no');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->select('id','emp_id', 'name','email','phone');
    }
}
