<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'document_type_id',
        'document'
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}