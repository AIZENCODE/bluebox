<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'etapa',
        'estado',
        'contract_id',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function proyect()
    {
        return $this->belongsTo(Proyect::class);
    }
}
