<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}