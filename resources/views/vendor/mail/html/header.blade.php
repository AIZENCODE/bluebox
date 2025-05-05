@props(['url'])
<tr>
<td class="header">
{{-- <a href="{{ $url }}" style="display: inline-block;"> --}}
@if (trim($slot) === 'Laravel')
{{-- <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo"> --}}
@else

{{-- produccion --}}
{{-- <img src="{{ url('img/logos/logo horizontal@4x.png') }}" class="logo" alt="Bluebox"
     style="display: block; max-width: 100%;
    border: none;
    height: 180px;
    max-height: 180px;
    width: 100%;
    display: block;
    min-width: 100%;
    min-height: 150px;
    margin: 0 auto;
    object-fit: contain;"> --}}
{{-- Fin produccion --}}



<img src="https://blueboxsolutions.tech/wp-content/uploads/2025/05/logo-horizontal@4x.png" class="logo" alt="Bluebox"
     style="display: block; max-width: 100%;
    border: none;
    height: 60px;
    max-height: 60px;
    width: 100%;
    display: block;
    min-width: 100%;
    min-height: 60px;
    margin: 0 auto;
    object-fit: contain;">


{{-- {!! $slot !!} --}}
@endif
</a>
</td>
</tr>
