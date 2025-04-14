<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'image_url',
        'description'
    ];

    // Post.php
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
