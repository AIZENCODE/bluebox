@section('title', 'Contactanos')
<x-layout-client>

    <section class="relative py-16 bg-white overflow-hidden h-screen grid">
        <!-- Fondo azul del lado izquierdo -->
        <div class="absolute inset-y-0 left-0 w-1/2 bg-blue-600 z-0"></div>
    
        <!-- Contenido principal encima -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 items-center gap-10">
            {{-- Columna izquierda: Texto sobre fondo azul --}}
            <div class="text-white space-y-4">
                <p class="flex items-center gap-2 text-sm font-semibold">
                    <x-heroicon-s-sparkles class="w-5 h-5 text-yellow-300" />
                    ¿Listo para comenzar?
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold">Comunícate Con Nosotros</h2>
                <p class="text-base">
                    Estamos aquí para ayudarte a llevar tu idea al siguiente nivel.
                </p>
            </div>
    
            {{-- Columna derecha: Formulario con fondo blanco --}}
            <div class="bg-white shadow-lg rounded-lg p-8">
                <h3 class="text-lg font-semibold text-blue-700 mb-6">
                    Déjanos tus datos y te contactamos pronto.
                </h3>
                <form class="space-y-4">
                    <input type="text" placeholder="Nombre" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                    <input type="email" placeholder="Email" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                    <input type="tel" placeholder="Número de celular" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                    <textarea placeholder="Mensaje" rows="4" class="w-full border border-gray-300 rounded-md px-4 py-2 resize-none"></textarea>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-semibold hover:bg-blue-700 transition">
                        Enviar
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    


</x-layout-client>
