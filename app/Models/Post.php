<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'image_url',
        'is_published',
        'published_at',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
