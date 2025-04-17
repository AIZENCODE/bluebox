<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $primaryKey = 'idProvince';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'idProvince',
        'province',
        'idDepartment',
    ];

    // Relación: una provincia pertenece a un departamento
    public function department()
    {
        return $this->belongsTo(Department::class, 'idDepartment', 'idDepartment');
    }
}
