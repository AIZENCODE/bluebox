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

    public function exchangeRatesFrom()
    {
        return $this->hasMany(Exchange::class, 'from_currency_id');
    }

    public function exchangeRatesTo()
    {
        return $this->hasMany(Exchange::class, 'to_currency_id');
    }
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

}
