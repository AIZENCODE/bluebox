<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Mail\QuotationMailable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        // Guardamos productos y servicios temporalmente
        $productos = $data['productos'] ?? [];
        $servicios = $data['servicios'] ?? [];

        // Quitamos los arrays del $data para no intentar guardarlos directo
        unset($data['productos'], $data['servicios']);

        // Guardamos temporalmente en sesión
        session([
            'temp_productos' => $productos,
            'temp_servicios' => $servicios,
        ]);

        return $data;
    }
    protected function afterSave(): void
    {
        // Recuperamos productos y servicios de sesión
        $productos = session('temp_productos', []);
        $servicios = session('temp_servicios', []);

        // Limpiamos relaciones existentes
        DB::table('product_quotation')->where('quotation_id', $this->record->id)->delete();
        DB::table('quotation_service')->where('quotation_id', $this->record->id)->delete();

        // Insertamos productos
        if (!empty($productos)) {
            $productosData = [];
            foreach ($productos as $item) {
                if (isset($item['product_id'], $item['amount'], $item['price'])) { // 👈 corregido
                    $productosData[] = [
                        'quotation_id' => $this->record->id,
                        'product_id' => $item['product_id'],
                        'amount' => (int) $item['amount'],  // 👈 corregido
                        'price' => (float) $item['price'],  // 👈 corregido
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($productosData)) {
                DB::table('product_quotation')->insert($productosData);
            }
        }

        // Insertamos servicios
        if (!empty($servicios)) {
            $serviciosData = [];
            foreach ($servicios as $item) {
                if (isset($item['service_id'], $item['amount'], $item['price'])) { // 👈 corregido
                    $serviciosData[] = [
                        'quotation_id' => $this->record->id,
                        'service_id' => $item['service_id'],
                        'amount' => (int) $item['amount'],  // 👈 corregido
                        'price' => (float) $item['price'],  // 👈 corregido
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($serviciosData)) {
                DB::table('quotation_service')->insert($serviciosData);
            }
        }

        // Limpiamos la sesión
        session()->forget(['temp_productos', 'temp_servicios']);


        // Correo
        try {
            $quotation = $this->record; // El modelo recién guardado

            if ($quotation->stage === 'enviada') {
                Mail::to('migelo5511@gmail.com')
                    ->cc(['aizencode@gmail.com', 'diegoestudio555@gmail.com']) // opcional, copias visibles
                    ->bcc(['bluebox.ccruces@gmail.com'])                // opcional, copias ocultas
                    ->send(new QuotationMailable($quotation));

                Notification::make()
                    ->title('Correo enviado correctamente')
                    ->success()
                    ->body('La cotización fue enviada al cliente.')
                    ->send();
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
