<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
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

    ];

    public function quotations()
    {
        return $this->belongsToMany(Quotation::class)
            ->withPivot(['amount', 'price'])
            ->withTimestamps();
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
