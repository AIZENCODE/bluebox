<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Mail\QuotationMailable;
use App\Models\Data;
use Barryvdh\DomPDF\Facade\Pdf;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

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
        $contract = $this->record;

        $this->guardarProductosYServicios();
        $this->enviarCorreoSiEsNecesario($contract);

        if ($contract->stage === 'aceptada') {
            $this->crearProyecto($contract);
        }
    }

    protected function guardarProductosYServicios(): void
    {
        DB::table('contract_product')->where('contract_id', $this->record->id)->delete();
        DB::table('contract_service')->where('contract_id', $this->record->id)->delete();

        $productos = session('temp_productos', []);
        $servicios = session('temp_servicios', []);

        if (!empty($productos)) {
            $productosData = collect($productos)->filter(fn($item) => isset($item['product_id'], $item['amount'], $item['price']))->map(fn($item) => [
                'contract_id' => $this->record->id,
                'product_id' => $item['product_id'],
                'amount' => (int) $item['amount'],
                'price' => (float) $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('contract_product')->insert($productosData);
        }

        if (!empty($servicios)) {
            $serviciosData = collect($servicios)->filter(fn($item) => isset($item['service_id'], $item['amount'], $item['price']))->map(fn($item) => [
                'contract_id' => $this->record->id,
                'service_id' => $item['service_id'],
                'amount' => (int) $item['amount'],
                'price' => (float) $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('contract_service')->insert($serviciosData);
        }

        session()->forget(['temp_productos', 'temp_servicios']);
    }

    protected function enviarCorreoSiEsNecesario($contract): void
    {
        try {
            $data = $this->form->getState();

            if ($contract->stage !== 'enviada') return;

            $folder = storage_path('app/pdfs/cotizaciones');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $filename = 'cotizacion-' . $this->record->id . '.pdf';
            $absolutePath = $folder . '/' . $filename;

            $pdf = Pdf::loadView('admin.Quotations.pdfs.pdf', [
                'contract' => $this->record->load(['products', 'services', 'companie', 'igv']),
                'companyInfo' => Data::first(),
            ]);

            file_put_contents($absolutePath, $pdf->output());

            $shouldSend = $contract->mail_date === null || ($data['mail'] ?? false);
            if (!$shouldSend) return;

            $to = $contract->companie?->mail ? [$contract->companie->mail] : [];
            $cc = optional($contract->companie)->clients?->whereNotNull('mail')->pluck('mail')->toArray() ?? [];

            if (empty($to) && empty($cc)) {
                Notification::make()->title('Sin correos válidos')->danger()->body('No se encontraron correos para enviar la cotización.')->send();
                return;
            }

            $contract->mail_date = now();
            $contract->save();

            Mail::to($to)->cc($cc)->bcc(['bluebox.ccruces@gmail.com'])->send(
                (new QuotationMailable($contract))->attach($absolutePath)
            );

            if (file_exists($absolutePath)) unlink($absolutePath);

            Notification::make()->title('Correo enviado correctamente')->success()->body('La cotización fue enviada al cliente.')->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error al enviar correo')->danger()->body('No se pudo enviar la cotización. ' . $e->getMessage())->send();
        }
    }

    protected function crearProyecto($contract): void
    {
        if ($contract->contract()->exists()) return;

       
    }



}
