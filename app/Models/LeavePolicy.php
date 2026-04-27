<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'title',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}
