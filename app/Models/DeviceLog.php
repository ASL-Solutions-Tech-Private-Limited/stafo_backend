<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'company_id',
        'log_data',
    ];
}
