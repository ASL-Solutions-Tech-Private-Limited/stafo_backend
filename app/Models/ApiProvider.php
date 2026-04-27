<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiProvider extends Model
{
    use HasFactory;
    protected $table = 'api_provider';

    protected $fillable = [
        'api_id',
        'operator_id',
        'api_code',
        'api_provider_code'
    ];

    public function operator()
    {
        return $this->belongsTo(BbpsOperatorMaster::class, 'operator_id');
    }
}