<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'simbolo',

    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
