<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'code',
        'name',
        'document',
        'start_date',
        'end_date',
        'stage',
        'state',

        // Relaciones
        'quotation_id',
        'igv_id',

        'user_id',
        'user_update_id',
        'currency_id',
        'companie_id',
        // Fin relaciones


    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }


    public function proyect()
    {
        return $this->belongsTo(Proyect::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'contract_product')
            ->withPivot('amount', 'price')
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'contract_service')
            ->withPivot('amount', 'price')
            ->withTimestamps();
    }

    public function igv()
    {
        return $this->belongsTo(Igv::class);
    }

    public function companie()
    {
        return $this->belongsTo(Companie::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
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
