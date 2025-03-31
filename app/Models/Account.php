<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    
    use SoftDeletes;


    protected $fillable = [
        'nombre',
        'tipo',
        'numero',
        'numero_interbancario',
        'estado',
        'bank_id',
        'accounttype_id',
        'currency_id',
        
    ];
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'accounttype_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function datas()
    {
        return $this->belongsToMany(Data::class);
    }
    


}
