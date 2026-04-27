<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInfo extends Model
{
    use HasFactory;

     protected $table = "customers";
      protected $fillable = [
        'company_id',
        'employee_id',
        'customer_name',
        'email',
        'phone',
        'address',
        'company_name',
        'notes',
    ];

    // Relationships
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}
