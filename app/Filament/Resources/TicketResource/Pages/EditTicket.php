<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected array $users = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['users'] = $this->record->users()->pluck('users.id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->users = $data['users'] ?? [];
        unset($data['users']);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->users()->sync($this->users);
    }

}
