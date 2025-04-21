<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Igv extends Model
{

    protected $fillable = [
        'type',
        'percentage',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }  
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }  



}
