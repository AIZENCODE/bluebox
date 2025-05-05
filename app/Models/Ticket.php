<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'code',
        'title',
        'document',
        'description',
        'priority',
        'status',
        'due_date',
        'resolved_at',

        'proyect_id',
        'user_id',
        'user_update_id',
    ];
    public function proyect()
    {
        return $this->belongsTo(Proyect::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
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
