<?php

namespace App\Observers;

use App\Models\Companie;
use Illuminate\Support\Facades\Auth;

class CompanieObserver
{
    public function creating(Companie $companie)
    {
        if (Auth::check()) {
            $companie->user_id = Auth::id();
        }  
    }

    public function updating(Companie $companie)
    {
        if (Auth::check()) {
            $companie->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
