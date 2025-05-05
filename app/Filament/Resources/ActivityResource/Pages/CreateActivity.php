<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected array $users = [];

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
