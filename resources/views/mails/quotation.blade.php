@component('mail::message')
{{-- # Cotización {{ $quotation->code }} --}}

Buen dia Estimados,

Ponemos a su disposición la cotización emitida por Bluebox, identificada con el código {{ $quotation->code }}.

@isset($quotation->mail_date)
Le reenviamos la presente cotización, originalmente enviada el **{{ \Carbon\Carbon::parse($quotation->mail_date)->format('d/m/Y') }}**.
@else
Adjunto encontrará la cotización solicitada.
@endisset

**Fecha de validez** de esta cotización: **{{ \Carbon\Carbon::parse($quotation->creation_date)->addDays($quotation->days)->format('d/m/Y') }}**

Agradecemos su interés en nuestros servicios. Si tiene alguna consulta o requiere una atención personalizada, no dude en contactarnos.

@component('mail::button', ['url' => config('app.url')])
Ir a Bluebox
@endcomponent

Saludos cordiales,  
**Bluebox**

@endcomponent
