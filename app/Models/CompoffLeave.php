<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompoffLeave extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'date',
        'status',
        'employee_id',
        'company_id',
        'filename'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->select('id','emp_id', 'name','email','phone');
    }
}
