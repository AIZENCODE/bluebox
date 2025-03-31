<?php

namespace App\Observers;

use App\Models\Quotation;

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

        $max = Quotation::withTrashed()->max('codigo'); // Incluye eliminados si usas SoftDeletes
        $number = $max ? ((int) str_replace('CTR-', '', $max)) + 1 : 1;
        $quotation->codigo = 'COT-' . str_pad($number, 9, '0', STR_PAD_LEFT);
        
    }

    /**
     * Handle the Quotation "updated" event.
     */
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
