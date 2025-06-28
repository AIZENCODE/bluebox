<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'dni',
        'mail',
        'phone_one',
        'phone_two',
        'state',

        // Relaciones
        'user_id',
        'user_update_id',
        // Fin relaciones
    ];

    public function companies()
    {
        return $this->belongsToMany(Companie::class);
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
