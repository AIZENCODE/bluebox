<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $primaryKey = 'idDistrict';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'idDistrict',
        'district',
        'idProvince',
    ];

    // Relación: un distrito pertenece a una provincia
    public function province()
    {
        return $this->belongsTo(Province::class, 'idProvince', 'idProvince');
    }
}
