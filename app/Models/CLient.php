<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nombre',
        'correo',
        'telefono_uno',
        'telefono_dos',
        'estado',
    ];

    public function companies()
    {
        return $this->belongsToMany(Companie::class);
    }

}
