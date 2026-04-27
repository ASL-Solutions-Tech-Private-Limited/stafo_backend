<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id', 'company_id','driver_id','user_type', 'action_type', 'latitude', 'longitude',
        'speed', 'odometer', 'image_path', 'timestamp'
    ];

    


    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
