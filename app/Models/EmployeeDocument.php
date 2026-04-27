<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = ['document_type_id', 'employee_id', 'document_name', 'file_path'];

    // Relationship with DocumentType
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}