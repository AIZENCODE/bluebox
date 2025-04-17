<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $primaryKey = 'idCountry';
    public $incrementing = false; // ⚠️ Importante: no es autoincremental
    protected $keyType = 'int';   // ⚠️ Importante: tipo de clave primaria

    protected $fillable = [
        'idCountry',
        'country',
        'nationality',
    ];
}
