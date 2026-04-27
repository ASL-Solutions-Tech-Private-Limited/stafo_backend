<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'company_id',
        'shift_id',
    ];
    
    
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Relationship with Employee model (if needed)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
