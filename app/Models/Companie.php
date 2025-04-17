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
        'idCountry',
        'idDepartment',
        'idProvince',
        'idDistrict',
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

    public function country()
    {
        return $this->belongsTo(Country::class,);
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'idDepartment', 'idDepartment');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'idProvince', 'idProvince');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'idDistrict', 'idDistrict');
    }


}
