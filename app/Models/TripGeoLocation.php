<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripGeoLocation extends Model
{
    use HasFactory;

     protected $fillable = [
        'trip_id',
        'company_id',
        'driver_id',
        'latitude',
        'longitude',
    ];
}
