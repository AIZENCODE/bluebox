<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',

        'user_id',
        'user_update_id',

    ];


    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class); // 👈 está bien así
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id'); // 👈 por claridad
    }
}
