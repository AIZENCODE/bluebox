<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'stage',
        'state',

        'proyect_id',
        'user_id',
        'user_update_id',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function proyect()
    {
        return $this->belongsTo(Proyect::class);
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
