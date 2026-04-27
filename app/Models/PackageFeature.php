<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    use HasFactory;
    protected $table = 'package_features';

    protected $fillable = ['package_id', 'features_id', 'feature_value', 'status'];
    public function features()
    {
        return $this->belongsTo(Features::class)->select('id', 'name', 'description');
         
    }
}