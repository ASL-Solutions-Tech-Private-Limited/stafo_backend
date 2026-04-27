<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpAndSupport extends Model
{
    use HasFactory;
    protected $table = 'help_and_support';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'contact_number',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
}