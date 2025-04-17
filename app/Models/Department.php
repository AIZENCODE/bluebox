<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'idDepartment';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'idDepartment',
        'name',
        'idCountry',
    ];

    // Relación: un departamento pertenece a un país
    public function country()
    {
        return $this->belongsTo(Country::class, 'idCountry', 'idCountry');
    }
}
