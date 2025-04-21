<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Data extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'ruc',
        'image_url',
        'phone_one',
        'phone_two',
        'email_one',
        'email_two',
        'address_one',
        'address_two',

        'user_id',
        'user_update_id',
    ];

    public function accounts()
    {
        return $this->belongsToMany(Account::class);
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
