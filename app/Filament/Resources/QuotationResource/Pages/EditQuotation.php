<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Guardamos los productos y servicios temporalmente
        $productos = $data['productos'] ?? [];
        $servicios = $data['servicios'] ?? [];

        // Removemos los arrays de productos y servicios del data
        unset($data['productos']);
        unset($data['servicios']);

        // Guardamos los datos temporales en la sesión
        session(['temp_productos' => $productos]);
        session(['temp_servicios' => $servicios]);

        return $data;
    }

    protected function afterSave(): void
    {
        // Obtener los datos temporales de la sesión
        $productos = session('temp_productos', []);
        $servicios = session('temp_servicios', []);

        // Limpiar las relaciones existentes
        DB::table('product_quotation')->where('quotation_id', $this->record->id)->delete();
        DB::table('quotation_service')->where('quotation_id', $this->record->id)->delete();

        // Procesamos los productos
        if (!empty($productos)) {
            $productosData = [];
            foreach ($productos as $item) {
                if (isset($item['product_id'], $item['cantidad'], $item['precio'])) {
                    $productosData[] = [
                        'quotation_id' => $this->record->id,
                        'product_id' => $item['product_id'],
                        'cantidad' => (int) $item['cantidad'],
                        'precio' => (float) $item['precio'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($productosData)) {
                DB::table('product_quotation')->insert($productosData);
            }
        }

        // Procesamos los servicios
        if (!empty($servicios)) {
            $serviciosData = [];
            foreach ($servicios as $item) {
                if (isset($item['service_id'], $item['cantidad'], $item['precio'])) {
                    $serviciosData[] = [
                        'quotation_id' => $this->record->id,
                        'service_id' => $item['service_id'],
                        'cantidad' => (int) $item['cantidad'],
                        'precio' => (float) $item['precio'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($serviciosData)) {
                DB::table('quotation_service')->insert($serviciosData);
            }
        }

        // Limpiamos los datos temporales de la sesión
        session()->forget(['temp_productos', 'temp_servicios']);
    }
}
