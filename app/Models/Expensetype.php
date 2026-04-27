<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expensetype extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_document_req',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }
    public function expenseForms()
    {
        return $this->hasMany(Expenseform::class, 'type_id');
    }
}
