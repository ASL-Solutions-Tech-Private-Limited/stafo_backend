<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbpsOperatorMaster extends Model
{
    use HasFactory;
    // protected $table = 'bbps_operator_master';
    protected $table = 'bbps_operator_master';

    protected $fillable = [
        'InCode',
        'operator_code',
        'name',
        'service',
        'category',
        'APICode',
        'viewbill',
        'blr_coverage',
        'regex',
        'displayname',
        'ad1_d_name',
        'ad1_name',
        'ad1_regex',
        'ad2_d_name',
        'ad2_name',
        'ad2_regex',
        'ad3_d_name',
        'ad3_name',
        'ad3_regex',
        'mode',
        'message',
        'status',
        'update_by',
        'update_dttm'
    ];

    public function apiProviders()
    {
        return $this->hasMany(ApiProvider::class, 'operator_id');
    }
}