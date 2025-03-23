<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Data extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'imagen_url',
        'telefono_uno',
        'telefono_dos',
        'correo_uno',
        'correo_dos',
        'direccion_uno',
        'direccion_dos',
    ];
    
}
