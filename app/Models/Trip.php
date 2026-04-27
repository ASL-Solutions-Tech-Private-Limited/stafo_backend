<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

      protected $fillable = [
        'title', 'vehicle_id', 'driver_id','company_id','employee_id','customer_id', 'start_location', 'end_location','start_latitude','start_longitude','end_latitude','end_longitude','distance',
        'start_time', 'end_time', 'notes', 'status','trip_started_by','from_address','to_address'
    ];

    protected $casts = [
    'distance' => 'float',
    ];

     public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'driver_id'); 
    }

    public function driver()
    {
            return $this->belongsTo(Employee::class, 'driver_id');
    }


    public function customerInfo()
    {
        return $this->belongsTo(CustomerInfo::class, 'customer_id');
    }

    public function expenses()
{
    return $this->hasMany(TripExpense::class);
}


public function tripLogs()
{
    return $this->hasMany(TripLog::class, 'trip_id');
}


}
