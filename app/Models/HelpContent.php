<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpContent extends Model
{
    use HasFactory;

    protected $table = 'help_contents';

    protected $fillable = [
        'title',
        'description',
        'type', // "note" or "video"
        'file_path', // Path to the uploaded file (nullable)
        'url',  // YouTube video URL (nullable for notes)
    ];
}