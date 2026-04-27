<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeGeoLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'latitude',
        'longitude',
        'battery_status',
    ];
}
