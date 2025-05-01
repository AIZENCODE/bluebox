<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()
                ->before(function ($action) {
                    if ($this->record->posts()->count() > 0) {
                        Notification::make()
                            ->title('No se puede eliminar la categoría')
                            ->body('Esta categoría tiene artículos asociados.')
                            ->danger()
                            ->send();

                        throw new Halt();
                    }
                }),
        ];
    }
}
