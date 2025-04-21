<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <title>{{ config('app.name') }} | @yield('title', config('app.name'))</title>

    {{-- Descripción para motores de búsqueda --}}
    <meta name="description" content="@yield('description', 'Creamos soluciones digitales como sitios web, tiendas online y apps a medida.')">

    {{-- Palabras clave (opcional) --}}
    <meta name="keywords" content="desarrollo web, tiendas online, sistemas, software, Laravel, BlueBox">

    {{-- Nombre del autor --}}
    <meta name="author" content="BlueBox">


    {{-- Ícono de pestaña / Favicon --}}
    <link rel="icon" href="{{ asset('img/logos/Icono@4x.ico') }}" type="image/x-icon">

    {{-- Open Graph para redes sociales (SEO en redes) --}}
    <meta property="og:title" content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta property="og:description" content="@yield('description', 'Creamos soluciones digitales como sitios web, tiendas online y apps a medida.')">
    <meta property="og:image" content="{{ asset('img/seo-preview.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Twitter Card (para vista previa en Twitter) --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name')) | {{ config('app.name') }}">
    <meta name="twitter:description" content="@yield('description', 'Creamos soluciones digitales como sitios web, tiendas online y apps a medida.')">
    <meta name="twitter:image" content="{{ asset('img/seo-preview.jpg') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />




    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="">


    @include('layouts.client.navbar')

    <div class="">
        <div class="">
            {{ $slot }}
        </div>
    </div>


    @include('layouts.client.footer')
    @livewireScripts


    @stack('scripts')
</body>

</html>
