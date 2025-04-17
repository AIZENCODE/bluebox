<x-layout-client>
    <section class="py-16">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Texto izquierdo --}}
            <div class="flex flex-col justify-center space-y-6 h-full">
                <p class="text-sm font-semibold text-blue-600 uppercase">Llevamos tus ideas a lo digital</p>
                <h1 class="text-4xl font-bold text-gray-900 leading-tight">
                    Creamos soluciones digitales <br> que <span class="text-blue-600">sí funcionan</span>
                </h1>
                <p class="text-gray-600 text-lg">
                    Desarrollamos páginas web, tiendas online, apps y sistemas a medida.
                </p>
                <div class="">
                    <a href="#contacto"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md">
                        Contáctanos
                    </a>
                </div>

            </div>

            {{-- Slider derecho --}}
            <div class="flex items-center justify-center">
                <div class="swiper w-full max-w-md h-[400px] rounded-2xl overflow-hidden relative">
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

    {{-- SwiperJS --}}
    @push('styles')
        <style>
            .swiper-slide {
                opacity: 0 !important;
                transition: opacity 0.5s ease-in-out;
                position: absolute !important;
                inset: 0;
            }

            .swiper-slide-active {
                opacity: 1 !important;
                position: relative !important;
            }

            .swiper-wrapper {
                position: relative;
                height: 100%;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    @endpush


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            new Swiper('.swiper', {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                effect: 'fade',
            });
        </script>
    @endpush
</x-layout-client>
