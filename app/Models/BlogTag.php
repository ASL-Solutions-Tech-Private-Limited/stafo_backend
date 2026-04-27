<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'status'];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag_relation', 'tag_id', 'blog_id');
    }
    public function blogTags()
    {
        return $this->hasMany(BlogTagRelation::class, 'tag_id');
    }
}
