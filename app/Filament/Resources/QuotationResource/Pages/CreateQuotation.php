<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Guardamos los productos y servicios temporalmente
        $productos = $data['productos'] ?? [];
        $servicios = $data['servicios'] ?? [];

        // Removemos los arrays de productos y servicios del data
        unset($data['productos']);
        unset($data['servicios']);

        // Aseguramos que los campos requeridos estén presentes
        $data = array_merge([
            'fecha_creacion' => now()->format('Y-m-d'),
            'estado' => true,
            'etapa' => 'borrador',
            'days' => 0,
        ], $data);

        // Guardamos los datos temporales en la sesión
        session(['temp_productos' => $productos]);
        session(['temp_servicios' => $servicios]);

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            // Obtener los datos temporales de la sesión
            $productos = session('temp_productos', []);
            $servicios = session('temp_servicios', []);

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
        } catch (\Exception $e) {
            Log::error('Error al guardar relaciones:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function afterSave(): void
    {
        try {
            // Obtener los datos del formulario
            $data = $this->form->getState();

            Log::info('Estado del formulario (save):', ['data' => $data]);

            // Limpiar relaciones existentes
            $this->record->products()->detach();
            $this->record->services()->detach();

            // Procesamos los productos
            if (isset($data['productos']) && is_array($data['productos'])) {
                foreach ($data['productos'] as $item) {
                    if (isset($item['product_id']) && isset($item['cantidad']) && isset($item['precio'])) {
                        $this->record->products()->attach($item['product_id'], [
                            'cantidad' => (int) $item['cantidad'],
                            'precio' => (float) $item['precio']
                        ]);
                    }
                }
            }

            // Procesamos los servicios
            if (isset($data['servicios']) && is_array($data['servicios'])) {
                foreach ($data['servicios'] as $item) {
                    if (isset($item['service_id']) && isset($item['cantidad']) && isset($item['precio'])) {
                        $this->record->services()->attach($item['service_id'], [
                            'cantidad' => (int) $item['cantidad'],
                            'precio' => (float) $item['precio']
                        ]);
                    }
                }
            }

            // Verificamos que se hayan guardado los datos
            $productosGuardados = $this->record->products()->withPivot(['cantidad', 'precio'])->get()->toArray();
            $serviciosGuardados = $this->record->services()->withPivot(['cantidad', 'precio'])->get()->toArray();

            Log::info('Productos guardados (save):', ['productos' => $productosGuardados]);
            Log::info('Servicios guardados (save):', ['servicios' => $serviciosGuardados]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar relaciones:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
