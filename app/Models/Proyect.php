<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyect extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
        'etapa',
        'contract_id',

    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
