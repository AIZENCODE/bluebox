<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'creation_date',
        'days',
        'stage',
        'state',
        // 'mail',
        'mail_date',

        // Relaciones
        'igv_id',
        'companie_id',
        'user_id',
        'user_update_id',
        'currency_id',
        // Fin relaciones
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
            ->withPivot('amount', 'price')
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'quotation_service')
            ->withPivot('amount', 'price')
            ->withTimestamps();
    }


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function igv()
    {
        return $this->belongsTo(Igv::class);
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
