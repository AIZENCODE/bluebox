<?php

namespace App\Observers;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountObserver
{
    
    public function creating(Account $account)
    {
        if (Auth::check()) {
            $account->user_id = Auth::id();
        }  
    }

    public function updating(Account $account)
    {
        if (Auth::check()) {
            $account->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
