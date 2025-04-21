@section('title', 'Inicio')
<x-layout-client>

    {{-- SwiperJS --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <style>
            .swiper-header .swiper-slide {
                opacity: 0 !important;
                transition: opacity 0.5s ease-in-out;
                position: absolute !important;
                inset: 0;
            }

            .swiper-header .swiper-slide-active {
                opacity: 1 !important;
                position: relative !important;
            }

            .swiper-header .swiper-wrapper {
                position: relative;
                height: 100%;
            }

            .logosSwiper .swiper-slide {
                width: auto !important;
            }
        </style>
    @endpush




    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:flex gap-10 justify-between items-center">
            {{-- Texto izquierdo --}}
            <div class="flex flex-col justify-center space-y-2 h-full max-w-2xl">
                <p class="text-sm font-semibold text-blue-600 uppercase">Llevamos tus ideas a lo digital</p>

                <div class="grid gap-5">
                    <h1 class="text-5xl font-bold text-gray-900">
                        Creamos soluciones digitales <br> que <span class="text-blue-600 ">sí funcionan</span>
                    </h1>
                    <p class="text-gray-600 text-md">
                        Diseñamos sitios web, tiendas online y sistemas personalizados que fortalecen tu presencia
                        digital. Combinamos diseño, tecnología y estrategia para lograr resultados concretos.
                    </p>

                    <div class="">
                        <a href="#contacto"
                            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md">
                            Contáctanos
                        </a>
                    </div>
                </div>


            </div>


            {{-- Slider derecho --}}
            <div class="flex items-center justify-center">
                <div class="swiper-header w-full max-w-md h-[400px] rounded-2xl overflow-hidden relative">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide flex items-center justify-center">
                            <img src="{{ asset('img/headers/slider-1.png') }}" alt="Slide 1"
                                class="h-full object-contain">
                        </div>
                        <div class="swiper-slide flex items-center justify-center">
                            <img src="{{ asset('img/headers/slider-2.png') }}" alt="Slide 2"
                                class="h-full object-contain">
                        </div>
                        <div class="swiper-slide flex items-center justify-center">
                            <img src="{{ asset('img/headers/slider-3.png') }}" alt="Slide 3"
                                class="h-full object-contain">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    @php
        $logos = [
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/0/08/Netflix_2015_logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e9/Notion-logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e9/Notion-logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e9/Notion-logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e9/Notion-logo.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e9/Notion-logo.svg',
        ];
        $logos = array_merge($logos, $logos, $logos); // mínimo 3 veces
    @endphp
    {{-- <section class="bg-blue-100 py-10 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="swiper logosSwiper">
                <div class="swiper-wrapper items-center">
                    @foreach ($logos as $logo)
                        <div class="swiper-slide flex justify-center items-center">
                            <div class="bg-white rounded-xl w-28 h-20 flex items-center justify-center shadow-md">
                                <img src="{{ $logo }}" alt="Logo" class="h-10 w-auto object-contain">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
     --}}










    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


        {{-- Slider de encabezado --}}
        <script>
            new Swiper('.swiper-header', {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                effect: 'fade',
            });
        </script>

        {{-- Carrusel de logos --}}
        {{-- <script>
            new Swiper(".logosSwiper", {
                loop: true,
                slidesPerView: 7, // Muestra 5 a la vez
                spaceBetween: 32,
                allowTouchMove: false, // No arrastrable
                autoplay: {
                    delay: 0, // Sin pausa
                    disableOnInteraction: false,
                },
                speed: 3000, // Velocidad constante
            });
        </script> --}}
    @endpush


</x-layout-client>
