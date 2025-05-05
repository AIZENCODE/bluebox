<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Mail\QuotationMailable;
use App\Models\Data;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
        $quotation = $this->record;

        $this->guardarProductosYServicios();
        $this->enviarCorreoSiEsNecesario($quotation);

        if ($quotation->stage === 'aceptada') {
            $this->crearContrato($quotation);
        }
    }

    protected function guardarProductosYServicios(): void
    {
        DB::table('product_quotation')->where('quotation_id', $this->record->id)->delete();
        DB::table('quotation_service')->where('quotation_id', $this->record->id)->delete();

        $productos = session('temp_productos', []);
        $servicios = session('temp_servicios', []);

        if (!empty($productos)) {
            $productosData = collect($productos)->filter(fn($item) => isset($item['product_id'], $item['amount'], $item['price']))->map(fn($item) => [
                'quotation_id' => $this->record->id,
                'product_id' => $item['product_id'],
                'amount' => (int) $item['amount'],
                'price' => (float) $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('product_quotation')->insert($productosData);
        }

        if (!empty($servicios)) {
            $serviciosData = collect($servicios)->filter(fn($item) => isset($item['service_id'], $item['amount'], $item['price']))->map(fn($item) => [
                'quotation_id' => $this->record->id,
                'service_id' => $item['service_id'],
                'amount' => (int) $item['amount'],
                'price' => (float) $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            DB::table('quotation_service')->insert($serviciosData);
        }

        session()->forget(['temp_productos', 'temp_servicios']);
    }

    protected function enviarCorreoSiEsNecesario($quotation): void
    {
        try {
            $data = $this->form->getState();

            if ($quotation->stage !== 'enviada') return;

            $folder = storage_path('app/pdfs/cotizaciones');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $filename = 'cotizacion-' . $this->record->id . '.pdf';
            $absolutePath = $folder . '/' . $filename;

            $pdf = Pdf::loadView('admin.Quotations.pdfs.pdf', [
                'quotation' => $this->record->load(['products', 'services', 'companie', 'igv']),
                'companyInfo' => Data::first(),
            ]);

            file_put_contents($absolutePath, $pdf->output());

            $shouldSend = $quotation->mail_date === null || ($data['mail'] ?? false);
            if (!$shouldSend) return;

            $to = $quotation->companie?->mail ? [$quotation->companie->mail] : [];
            $cc = optional($quotation->companie)->clients?->whereNotNull('mail')->pluck('mail')->toArray() ?? [];

            if (empty($to) && empty($cc)) {
                Notification::make()->title('Sin correos válidos')->danger()->body('No se encontraron correos para enviar la cotización.')->send();
                return;
            }

            $quotation->mail_date = now();
            $quotation->save();

            Mail::to($to)->cc($cc)->bcc(['bluebox.ccruces@gmail.com'])->send(
                (new QuotationMailable($quotation))->attach($absolutePath)
            );

            if (file_exists($absolutePath)) unlink($absolutePath);

            Notification::make()->title('Correo enviado correctamente')->success()->body('La cotización fue enviada al cliente.')->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error al enviar correo')->danger()->body('No se pudo enviar la cotización. ' . $e->getMessage())->send();
        }
    }

    protected function crearContrato($quotation): void
    {
        if ($quotation->contract()->exists()) return;

        // Crear contrato
        $contract = $quotation->contract()->create([
            'name' => $quotation->name,
            'quotation_id' => $quotation->id,
            'companie_id' => $quotation->companie_id,
            'igv_id' => $quotation->igv_id,
            // 'start_date' => now(),
            // 'end_date' => now()->addMonths(6),
            'status' => 'activo',
        ]);

        // Copiar productos
        $productos = DB::table('product_quotation')
            ->where('quotation_id', $quotation->id)
            ->get();

        foreach ($productos as $producto) {
            $contract->products()->attach($producto->product_id, [
                'amount' => $producto->amount,
                'price' => $producto->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Copiar servicios
        $servicios = DB::table('quotation_service')
            ->where('quotation_id', $quotation->id)
            ->get();

        foreach ($servicios as $servicio) {
            $contract->services()->attach($servicio->service_id, [
                'amount' => $servicio->amount,
                'price' => $servicio->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
