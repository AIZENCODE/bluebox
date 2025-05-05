<?php

namespace App\Observers;

use App\Models\Contract;

class ContractObserver
{
    /**
     * Handle the Contract "created" event.
     */


    public function creating(Contract $contract)
    {

        $max = Contract::withTrashed()->max('code'); // Incluye eliminados si usas SoftDeletes
        $number = $max ? ((int) str_replace('CTR-', '', $max)) + 1 : 1;
        $contract->code = 'CTR-' . str_pad($number, 9, '0', STR_PAD_LEFT);
        
    }

    public function created(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "updated" event.
     */
    public function updated(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "deleted" event.
     */
    public function deleted(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "restored" event.
     */
    public function restored(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "force deleted" event.
     */
    public function forceDeleted(Contract $contract): void
    {
        //
    }
}
