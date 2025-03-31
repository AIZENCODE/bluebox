<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_min',
        'precio_max',
        'estado',
    ];
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class);
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class);
    }
}
