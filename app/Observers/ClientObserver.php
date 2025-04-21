<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
      
    public function creating(Client $client)
    {
        if (Auth::check()) {
            $client->user_id = Auth::id();
        }  
    }

    public function updating(Client $client)
    {
        if (Auth::check()) {
            $client->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
