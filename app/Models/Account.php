<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    
    use SoftDeletes;


    protected $fillable = [
        'name',
        'number',
        'interbank_number',
        'state',

        // Relaciones
        'bank_id',
        'accounttype_id',
        'currency_id',

        'user_id',
        'user_update_id',
        // Fin relaciones
        
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
    
    public function user()
    {
        return $this->belongsTo(User::class); // 👈 está bien así
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id'); // 👈 por claridad
    }
    

}
