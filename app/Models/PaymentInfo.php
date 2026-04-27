<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'transaction_id',
        'package_id',
        'payment_amount',
        'message',
        'payment_info',
        'payment_status'
    ];
}
