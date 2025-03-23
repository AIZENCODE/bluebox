<?php

namespace App\Filament\Resources\DataResource\Pages;

use App\Filament\Resources\DataResource;
use App\Models\Data;
use Filament\Resources\Pages\EditRecord;

class EditData extends EditRecord
{
    protected static string $resource = DataResource::class;
    protected static ?string $title = 'Información de la Empresa';

    public function mount(string|int $record = null): void
    {
        if ($record) {
            $data = Data::find($record);
        } else {
            $data = Data::first();
        }

        if (!$data) {
            $data = Data::create([
                'nombre' => 'Mi Empresa',
                'razon_social' => 'Razón Social',
                'ruc' => '00000000000',
                'telefono_uno' => '123456789',
                'correo_uno' => 'empresa@example.com',
                'direccion_uno' => 'Dirección Principal',
            ]);
        }
        
        parent::mount($data->id);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function configureUpdateAction(): void
    {
        parent::configureUpdateAction();
        $this->updateAction->successRedirectUrl(static::getResource()::getUrl('index'));
    }
}
