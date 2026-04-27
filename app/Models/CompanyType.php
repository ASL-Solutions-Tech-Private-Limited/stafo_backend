<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    use HasFactory;
    protected $fillable = ['company_name', 'status'];

    public function companyDetails()
    {
        return $this->hasMany(CompanyDetail::class);
    }
}
