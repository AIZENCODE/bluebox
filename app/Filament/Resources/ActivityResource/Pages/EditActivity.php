<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

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
