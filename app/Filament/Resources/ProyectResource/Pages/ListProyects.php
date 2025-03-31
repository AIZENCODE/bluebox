<?php

namespace App\Filament\Resources\ProyectResource\Pages;

use App\Filament\Resources\ProyectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProyects extends ListRecords
{
    protected static string $resource = ProyectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
