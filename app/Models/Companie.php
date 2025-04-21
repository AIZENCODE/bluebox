<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companie extends Model
{

    // Si tu tabla se llama 'companies' pero el modelo es 'Companie'
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'company_name',
        'ruc',
        'mail',
        'phone',
        'address',
        'state',

        // Relaciones
        'idCountry',
        'idDepartment',
        'idProvince',
        'idDistrict',

        'user_id',
        'user_update_id',
        // Fin relaciones

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

    public function user()
    {
        return $this->belongsTo(User::class); // 👈 está bien así
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id'); // 👈 por claridad
    }

}
