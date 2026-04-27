<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leavetype extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'name',
        'no_of_days',
        'is_paid',
        'description',
        'status', // status column add kiya
    ];
}
