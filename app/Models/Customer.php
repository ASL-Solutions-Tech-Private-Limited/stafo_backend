<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['slab','first_name', 'last_name', 'phone','address','leaf','tobaco','thread'];

    public function slab(){
        return $this->belongsTo(Slab::class);
    }
}
