<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companie extends Model
{
    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'correo',
        'telefono',
        'direccion',
        'estado',
        
    ];
}
