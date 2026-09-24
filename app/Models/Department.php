<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'status', 'company_id'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}