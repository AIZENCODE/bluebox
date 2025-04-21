<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price_min',
        'price',
        'price_max',
        'state',

        // Relaciones
        'user_id',
        'user_update_id',
        // Fin relaciones
    ];
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class);
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class);
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
