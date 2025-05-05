<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotización - {{ $quotation->compact_order }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
            color: #000;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sub-title {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .info p {
            margin: 0 0 4px 0;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .totals {
            text-align: right;
            font-size: 13px;
            margin-top: 20px;
        }

        .totals p {
            margin: 3px 0;
        }

        .observacion {
            margin-top: 20px;
            font-style: italic;
            font-size: 11px;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $fechaReferencia = $quotation->updated_at ?? $quotation->created_at;
        $fechaVencimiento = $fechaReferencia->copy()->addDays($quotation->days ?? 0);
    @endphp
    {{-- Cabecera de empresa con logo y datos --}}
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 30%;">
                {{-- @if (!empty($companyInfo->image_url)) --}}
                <img src="{{ public_path('img/logos/logo horizontal@4x.png') }}" style="max-width: 100%; height: auto;">
                {{-- @endif --}}
            </td>
            <td style="width: 70%; text-align: right; font-size: 12px;">
                <strong>{{ $companyInfo->company_name }}</strong><br>
                RUC: {{ $companyInfo->ruc ?? '-' }}<br>
                {{ $companyInfo->address_one ?? '-' }}<br>
                @if (!empty($companyInfo->phone_one) || !empty($companyInfo->phone_two))
                    Tel:
                    {{ $companyInfo->phone_one ?? '' }}{{ $companyInfo->phone_two ? ' / ' . $companyInfo->phone_two : '' }}<br>
                @endif
                @if (!empty($companyInfo->email_one) || !empty($companyInfo->email_two))
                    Email:
                    {{ $companyInfo->email_one ?? '' }}{{ $companyInfo->email_two ? ' / ' . $companyInfo->email_two : '' }}
                @endif
            </td>
        </tr>
    </table>


    <div class="title">COTIZACIÓN</div>
    <div class="sub-title">
        N°: {{ $quotation->code }} &nbsp; | &nbsp;
        Fecha: {{ $fechaReferencia->format('d/m/Y') }} &nbsp; | &nbsp;
        Válido hasta: {{ $fechaVencimiento->format('d/m/Y') }}
    </div>

    <div class="section info">
        <p><strong>Cliente:</strong> {{ $quotation->companie->name ?? '-' }}</p>
        <p><strong>RUC:</strong> {{ $quotation->companie->ruc ?? '-' }}</p>
    </div>

    <div class="section">
        <h3>Detalle de Productos y/o Servicios</h3>
        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>P. Unitario (S/)</th>
                    <th>Total (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotation->products as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->pivot->amount }}</td>
                        <td>{{ number_format($item->pivot->price, 2) }}</td>
                        <td>{{ number_format($item->pivot->amount * $item->pivot->price, 2) }}</td>
                    </tr>
                @endforeach

                @foreach ($quotation->services as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->pivot->amount }}</td>
                        <td>{{ number_format($item->pivot->price, 2) }}</td>
                        <td>{{ number_format($item->pivot->amount * $item->pivot->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $subtotal = 0;

        foreach ($quotation->products as $product) {
            $subtotal += $product->pivot->amount * $product->pivot->price;
        }

        foreach ($quotation->services as $service) {
            $subtotal += $service->pivot->amount * $service->pivot->price;
        }

        $igvPercentage = $quotation->igv?->percentage ?? 0;
        $igvAmount = $subtotal * ($igvPercentage / 100);
        $total = $subtotal + $igvAmount;
    @endphp

    <div class="totals">
        <p><strong>Subtotal:</strong> S/ {{ number_format($subtotal, 2) }}</p>
        <p><strong>IGV ({{ $igvPercentage }}%):</strong> S/ {{ number_format($igvAmount, 2) }}</p>
        <p><strong>Total a Pagar:</strong> <strong>S/ {{ number_format($total, 2) }}</strong></p>
    </div>





    <div class="observacion">
        <p><strong>Observación:</strong> Esta cotización {{ $igvPercentage > 0 ? 'incluye IGV' : 'no incluye IGV' }}.
        </p>
    </div>

    <div class="footer">
        Bluebox S.A.C. | www.blueboxsolutions.tech | info@blueboxsolutions.tech
    </div>

</body>

</html>
