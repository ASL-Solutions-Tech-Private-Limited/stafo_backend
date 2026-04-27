<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripExpense extends Model
{
    use HasFactory;

      protected $fillable = [
        'trip_id',
        'company_id',
        'driver_id', 
        'expense_type',
        'amount',
        'note',
        'bill_receipt'
    ];

      public function trip()
      {
        return $this->belongsTo(Trip::class);
       }

     public function company()
     {
    return $this->belongsTo(CompanyDetail::class, 'company_id');
     }
}
