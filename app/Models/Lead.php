<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'employee_id',
        'name',
        'company_name',
        'company_address',   
        'email',
        'phone',
        'notes',
        'status',        
        'lead_from',
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
