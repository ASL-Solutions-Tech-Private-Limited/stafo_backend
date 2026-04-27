<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'employee_id',
        'company_id',
        'comments',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->select('id','emp_id','name','email','phone');
    }
}
