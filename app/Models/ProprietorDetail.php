<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Foundation\Auth\User as Authenticatable; // Use Authenticatable class
//use Laravel\Sanctum\HasApiTokens; // Add this import

class ProprietorDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'email',
        'password',
        'aadhar',
        'pan',
        'current_address',
        'current_city',
        'current_state',
        'current_country',
        'current_pin',
        'p_address',
        'p_city',
        'p_state',
        'p_country',
        'p_pin',
        'status',
        'company_id',
    ];

    protected $hidden = [
        'password', // Hide password attribute for security reasons
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}