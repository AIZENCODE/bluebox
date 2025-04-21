<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'creation_date',
        'days',
        'stage',
        'state',

        // Relaciones
        'igv_id',
        'companie_id',
        'user_id',
        'user_update_id',
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

    
    public function user()
    {
        return $this->belongsTo(User::class); // 👈 está bien así
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id'); // 👈 por claridad
    }

}
