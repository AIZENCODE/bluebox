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
        'bank_id',
        'numero',
        'numero_interbancario',
        'estado',
        
    ];
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
