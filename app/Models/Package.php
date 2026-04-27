<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Package extends Model
{
    use HasFactory;

    protected $fillable = ['package_name', 'description', 'price', 'discount_price', 'days', 'status', 
        'monthly_price', 'quarterly_price', 'halfyearly_price', 'yearly_price',
        'monthly_discount_price', 'quarterly_discount_price', 'halfyearly_discount_price', 'yearly_discount_price'];

    public function features()
    {
        return $this->belongsToMany(Features::class, 'package_features')
            ->withPivot('feature_value', 'status')
            ->withTimestamps();
    }
}