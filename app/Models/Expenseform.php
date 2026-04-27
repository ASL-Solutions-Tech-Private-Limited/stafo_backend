<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenseform extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type_id',
        'field_name',
        'field_type',
        'description',
        'field_type',
        'status',
    ];
}
