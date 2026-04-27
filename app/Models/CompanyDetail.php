<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable; // Use Authenticatable class
use Laravel\Sanctum\HasApiTokens; // Add this import
use Illuminate\Notifications\Notifiable;

class CompanyDetail extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'proprietor_id',
        'company_name',
        'image_name',
        'company_code',
        'company_type',
        'business_type_id',
        'registration_number',
        'gst_number',
        'pan_number',
        'address',
        'city',
        'state',
        'country',
        'pin',
        'bank_name',
        'account_number',
        'ifsc_code',
        'no_of_employee',
        'status',
        'mobile_no',
        'email',
        'password',
        'referral_code',
        'referred_by',
        'referralcode_used',
        'device_id',
        'fcm_token',
        'pan_verify',
        'aadhar_response',
        'pan_response',
        'reg_response',
        'gst_response',
        'max_employee_add',
        'employee_added',
        'subscription_start',
        'subscription_end',
        'map_view',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class, 'company_type'); // foreign key is company_type
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id'); // foreign key is business_type_id
    }
    public function referrals()
    {
        return $this->hasMany(CompanyDetail::class,'referralcode_used','referral_code'); // foreign key is referred_by and local key is referral_code
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}