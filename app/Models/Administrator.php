<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'short_description',
        'long_description',
        'meta_title',
        'meta_description',
        'image',
    ];
}
