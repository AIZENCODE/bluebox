<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function creating(Ticket $ticket)
    {

        if (Auth::check()) {
            $ticket->user_id = Auth::id();
        }

        $lastNumber = Ticket::withTrashed()
            ->where('code', 'like', 'TCK-%')
            ->selectRaw('MAX(CAST(SUBSTRING(code, 5) AS UNSIGNED)) as max_code')
            ->value('max_code');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        $ticket->code = 'TCK-' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);

    }

    
    public function updating(Ticket $ticket)
    {
        if (Auth::check()) {
            $ticket->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }

}
