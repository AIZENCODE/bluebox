<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'description',
        'symbol',

    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
