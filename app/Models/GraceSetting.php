<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraceSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'name',
        'label',
        'value',
        'status',
    ];
}
