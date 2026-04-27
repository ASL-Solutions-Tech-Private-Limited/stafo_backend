<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_name',
        'branch_address',
        'latitude',
        'longitude',
        'radar',
        'status',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}