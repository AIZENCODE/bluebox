<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{



    public function creating(Category $category)
    {

        if (Auth::check()) {
            $category->user_id = Auth::id();
        }


        // Evita sobrescribir si ya viene seteado desde un factory
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name, '-');
        }
    }

    public function updating(Category $category)
    {
        if (Auth::check()) {
            $category->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }


        // Si quieres actualizar el slug cuando cambia el título:
        if ($category->isDirty('name')) {
            $category->slug = Str::slug($category->name, '-');
        }
    }




}
