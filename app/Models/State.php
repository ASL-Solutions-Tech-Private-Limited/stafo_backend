<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'name',
        'iso_code',
        'status'
    ];

    public function companyDetails()
    {
        return $this->hasMany(CompanyDetail::class, 'state'); // Assuming 'state' is the foreign key
    }
}