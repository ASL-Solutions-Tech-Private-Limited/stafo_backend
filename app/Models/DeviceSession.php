<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    use HasFactory;
    protected $table = 'device_sessions';

    protected $fillable = [
        'employee_id',
        'company_id',
        'employee_device_id',
        'company_device_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function companyDetail()
    {
        return $this->belongsTo(CompanyDetail::class);
    }
}