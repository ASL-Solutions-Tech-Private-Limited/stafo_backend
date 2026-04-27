<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DcCcBbpsSubCategory extends Model
{
    use HasFactory;

    protected $table = 'dc_cc_bbps_sub_category';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category',
        'name',
        'code',
        'img',
        'status',
        'del',
        'update_by',
        'update_dttm',
        'order_by'
    ];
}