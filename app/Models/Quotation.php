<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'fecha_creacion',
        'days',
        'etapa',
        'estado',
        'companie_id',
        'igv_id',
    ];

    public function companie()
    {
        return $this->belongsTo(Companie::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quotation')
            ->withPivot('cantidad', 'precio')
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'quotation_service')
            ->withPivot('cantidad', 'precio')
            ->withTimestamps();
    }

    public function igv()
    {
        return $this->belongsTo(Igv::class);
    }
}
