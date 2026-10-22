<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbpsServiceCategory extends Model
{
    use HasFactory;
    protected $table = 'pz_service_category';
    protected $fillable = [
        'id','service_id','name','category_icon','category_file_name','category_code','commission','isFlat','created_at','created_by','updated_at','updated_by','service_code','status','orderBy'
    ];
}