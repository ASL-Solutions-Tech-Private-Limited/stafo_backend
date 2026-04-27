<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_no',
        'vehicle_type',
        'fuel',
        'load_capacity',
        'speedometer',
        'rc_upload_path',
        'rc_number',
        'driver_id',
        'company_id',
        'status',
        'km_travelled'
    ];

    // Optional relationship if you want to link to Employee (Driver)
    public function driver()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company()
    {
    return $this->belongsTo(CompanyDetail::class, 'company_id');
   }

}
