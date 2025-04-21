<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
   
    public function creating(Product $product)
    {
        if (Auth::check()) {
            $product->user_id = Auth::id();
        }  
    }

    public function updating(Product $product)
    {
        if (Auth::check()) {
            $product->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }

    }
}
