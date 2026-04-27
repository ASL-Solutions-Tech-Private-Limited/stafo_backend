<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_features')
            ->withPivot('feature_value', 'status')
            ->withTimestamps();
    }
}