<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */

    public function creating(Post $post)
    {

        if (Auth::check()) {
            $post->author_id = Auth::id();
            $post->user_id = Auth::id();
        }

        // Evita sobrescribir si ya viene seteado desde un factory
        if (empty($post->slug)) {
            $post->slug = Str::slug($post->title, '-');
        }
    }

    public function updating(Post $post)
    {
        if (Auth::check()) {
            $post->updated_by = Auth::id(); // o editor_id, según tu estructura
        }

        // Si quieres actualizar el slug cuando cambia el título:
        if ($post->isDirty('title')) {
            $post->slug = Str::slug($post->title, '-');
        }
    }

    public function created(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
