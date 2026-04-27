<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTagRelation extends Model
{
    use HasFactory;
    protected $fillable = [
        'blog_id',
        'tag_id'
    ];
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
    public function tag()
    {
        return $this->belongsTo(BlogTag::class, 'tag_id');
    }
    
}
