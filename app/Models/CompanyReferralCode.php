<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'referralcode',
        'start_date',
        'end_date',
        'use_count',
        'max_use_count',
        'status',
    ];

    public function referrals()
    {
        return $this->hasmany(CompanyDetail::class,'referralcode_used','referralcode'); // foreign key is referred_by and local key is referral_code
    }

}
