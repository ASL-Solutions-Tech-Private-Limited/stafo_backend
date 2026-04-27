<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name']; // Add your attributes here

    public function companyDetails()
    {
        return $this->hasMany(CompanyDetail::class, 'city'); // Assuming 'city' is the foreign key
    }
}