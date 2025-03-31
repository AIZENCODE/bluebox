<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'codigo',
        'fecha_creacion',
        'fecha_vencimiento',
        'etapa',
        'estado',
        'quotation_id',
        'igv_id',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    public function igv()
    {
        return $this->belongsTo(Igv::class);
    }
}
