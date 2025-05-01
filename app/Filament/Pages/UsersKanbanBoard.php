<?php

namespace App\Filament\Pages;

use App\Models\User;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class UsersKanbanBoard extends KanbanBoard
{

    protected static string $model = User::class;
    // protected static string $statusEnum = UserStatus::class;
}
