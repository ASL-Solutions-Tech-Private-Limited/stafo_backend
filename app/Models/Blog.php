<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'auther',
        'date',
        'short_description',
        'description',
        'image',
        'views',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag_relations', 'blog_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

}
