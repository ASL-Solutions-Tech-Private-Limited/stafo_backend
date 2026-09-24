<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePermissionOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'permission_key',
        'is_granted',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
