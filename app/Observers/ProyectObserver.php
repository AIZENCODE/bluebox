<?php

namespace App\Observers;

use App\Models\Proyect;
use Illuminate\Support\Facades\Auth;

class ProyectObserver
{
    public function creating(Proyect $proyect)
    {
        $max = Proyect::withTrashed()->max('code'); // Incluye eliminados si usas SoftDeletes
        $number = $max ? ((int) str_replace('PROY-', '', $max)) + 1 : 1;
        $proyect->code = 'PROY-' . str_pad($number, 9, '0', STR_PAD_LEFT);

        if (Auth::check()) {
            $proyect->user_id = Auth::id();
        }  
    }

    public function updating(Proyect $proyect)
    {
        if (Auth::check()) {
            $proyect->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
