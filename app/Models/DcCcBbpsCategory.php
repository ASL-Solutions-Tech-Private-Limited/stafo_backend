<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcCcBbpsCategory extends Model
{
    use HasFactory;

    protected $table = 'dc_cc_bbps_category';

    protected $fillable = [
        'id',
        'name',
        'code',
        'img',
        'status',
        'del',
        'update_by',
        'update_dttm',
        'orderBy'
    ];
}