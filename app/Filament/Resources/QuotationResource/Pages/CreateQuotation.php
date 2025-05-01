<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Mail\QuotationMailable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Guardamos los productos y servicios temporalmente
        $productos = $data['productos'] ?? [];
        $servicios = $data['servicios'] ?? [];

        // Removemos del $data principal
        unset($data['productos']);
        unset($data['servicios']);

        // Guardamos temporalmente en sesión
        session([
            'temp_productos' => $productos,
            'temp_servicios' => $servicios,
        ]);

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            // Recuperar los datos temporales
            $productos = session('temp_productos', []);
            $servicios = session('temp_servicios', []);

            // Procesar productos
            if (!empty($productos)) {
                $productosData = [];
                foreach ($productos as $item) {
                    if (isset($item['product_id'], $item['amount'], $item['price'])) {
                        $productosData[] = [
                            'quotation_id' => $this->record->id,
                            'product_id' => $item['product_id'],
                            'amount' => (int) $item['amount'],   // 👈 CORRECTO
                            'price' => (float) $item['price'],   // 👈 CORRECTO
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($productosData)) {
                    DB::table('product_quotation')->insert($productosData);
                }
            }

            // Procesar servicios
            if (!empty($servicios)) {
                $serviciosData = [];
                foreach ($servicios as $item) {
                    if (isset($item['service_id'], $item['amount'], $item['price'])) {
                        $serviciosData[] = [
                            'quotation_id' => $this->record->id,
                            'service_id' => $item['service_id'],
                            'amount' => (int) $item['amount'],   // 👈 CORRECTO
                            'price' => (float) $item['price'],   // 👈 CORRECTO
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (!empty($serviciosData)) {
                    DB::table('quotation_service')->insert($serviciosData);
                }
            }

            // Limpiar la sesión
            session()->forget(['temp_productos', 'temp_servicios']);
        } catch (\Exception $e) {
            Log::error('Error al guardar relaciones en CreateQuotation:', [
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
        // Correo
        try {
            $quotation = $this->record; // El modelo recién guardado

            if ($quotation->stage === 'enviada') {
                $company = optional($quotation->companie);

                $companyEmail = $company->email;
                $clientEmails = $company->clients?->pluck('email')->filter()->toArray() ?? [];

                if ($companyEmail) {
                    // Caso 1: Tiene correo la compañía
                    Mail::to($companyEmail)
                        ->cc($clientEmails)
                        ->bcc(['bluebox.ccruces@gmail.com'])
                        ->send(new QuotationMailable($quotation));

                    Notification::make()
                        ->title('Correo enviado correctamente')
                        ->success()
                        ->body('La cotización fue enviada al correo principal de la compañía y copias a sus clientes.')
                        ->send();
                } elseif (!empty($clientEmails)) {
                    // Caso 2: No hay correo en la compañía, pero sí en los clientes
                    Mail::to($clientEmails)
                        ->bcc(['bluebox.ccruces@gmail.com'])
                        ->send(new QuotationMailable($quotation));

                    Notification::make()
                        ->title('Correo enviado correctamente')
                        ->success()
                        ->body('La cotización fue enviada a los clientes asociados, ya que la compañía no tenía correo.')
                        ->send();
                } else {
                    // Caso 3: No hay ningún correo disponible
                    Notification::make()
                        ->title('No se pudo enviar el correo')
                        ->danger()
                        ->body('No se encontró correo de la compañía ni de los clientes para enviar la cotización.')
                        ->send();
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al enviar correo')
                ->danger()
                ->body('No se pudo enviar la cotización. Revisa el correo o inténtalo de nuevo.')
                ->send();
        }

        // Fin correo

    }
}
