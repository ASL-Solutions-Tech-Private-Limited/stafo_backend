<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'dial_code',
        'alpha_2_code',
        'alpha_3_code',
        'currency_symbol',
        'currency_name',
        'currency_name_sf',
        'status'
    ];

    public function companyDetails()
    {
        return $this->hasMany(CompanyDetail::class, 'country'); // Assuming 'country' is the foreign key
    }
}