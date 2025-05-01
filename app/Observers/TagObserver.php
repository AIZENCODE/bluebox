<?php

namespace App\Observers;

use App\Models\Tag;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TagObserver
{


    public function creating(Tag $tag)
    {

        if (Auth::check()) {
            $tag->user_id = Auth::id();
        }


        // Evita sobrescribir si ya viene seteado desde un factory
        if (empty($tag->slug)) {
            $tag->slug = Str::slug($tag->name, '-');
        }
    }

    public function updating(Tag $tag)
    {
        if (Auth::check()) {
            $tag->user_update_id = Auth::id(); // o editor_id, según tu estructura
        }


        // Si quieres actualizar el slug cuando cambia el título:
        if ($tag->isDirty('name')) {
            $tag->slug = Str::slug($tag->name, '-');
        }
    }


}
