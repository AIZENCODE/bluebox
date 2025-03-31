<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companie extends Model
{

    // Si tu tabla se llama 'companies' pero el modelo es 'Companie'
    protected $table = 'companies';
    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'correo',
        'telefono',
        'direccion',
        'estado',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }
    public function quotation()
    {
        return $this->hasMany(Quotation::class);
    }
}
