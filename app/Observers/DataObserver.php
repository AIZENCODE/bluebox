<?php

namespace App\Observers;

use App\Models\Data;
use Illuminate\Support\Facades\Auth;

class DataObserver
{

    public function creating(Data $data)
    {
        if (Auth::check()) {
            $data->user_id = Auth::id();
        }  
    }

    public function updating(Data $data)
    {
        if (Auth::check()) {
            $data->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
