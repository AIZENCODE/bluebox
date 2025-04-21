<?php

namespace App\Observers;

use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;

class QuotationObserver
{
    /**
     * Handle the Quotation "created" event.
     */
    public function created(Quotation $quotation): void
    {
        //
    }

    public function creating(Quotation $quotation)
    {

        if (Auth::check()) {
            $quotation->user_id = Auth::id();
        }

        $lastNumber = Quotation::withTrashed()
            ->where('codigo', 'like', 'COT-%')
            ->selectRaw('MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)) as max_codigo')
            ->value('max_codigo');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        $quotation->codigo = 'COT-' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT);
    }
    /**
     * Handle the Quotation "updated" event.
     */

    public function updating(Quotation $quotation)
    {
        if (Auth::check()) {
            $quotation->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }
    }
    public function updated(Quotation $quotation): void
    {
        //
    }

    /**
     * Handle the Quotation "deleted" event.
     */
    public function deleted(Quotation $quotation): void
    {
        //
    }

    /**
     * Handle the Quotation "restored" event.
     */
    public function restored(Quotation $quotation): void
    {
        //
    }

    /**
     * Handle the Quotation "force deleted" event.
     */
    public function forceDeleted(Quotation $quotation): void
    {
        //
    }
}
