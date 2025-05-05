<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;


    protected array $users = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->users = $data['users'] ?? [];
        unset($data['users']);
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Obtenemos los usuarios desde el estado del formulario
        $users = $this->form->getState()['users'] ?? [];

        // Sincronizamos los usuarios relacionados
        $this->record->users()->sync($users);
    }
}
