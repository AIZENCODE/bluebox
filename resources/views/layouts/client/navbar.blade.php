<!-- Public Navigation -->
<header class="bg-white shadow-md z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <a href="/" class="text-2xl font-bold text-blue-600">BlueBox</a>
        </div>

        <!-- Links -->
        <div class="hidden md:flex space-x-6">
          <a href="/" class="text-gray-700 hover:text-blue-600 transition">Inicio</a>
          <a href="#features" class="text-gray-700 hover:text-blue-600 transition">Características</a>
          <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Contacto</a>
        </div>

        <!-- Call to action -->
        <div class="hidden md:flex">
          <a href="/admin" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Iniciar sesión
          </a>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button id="mobile-menu-button" class="text-gray-600 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden px-4 pt-2 pb-4 space-y-2">
      <a href="/" class="block text-gray-700 hover:text-blue-600 transition">Inicio</a>
      <a href="#features" class="block text-gray-700 hover:text-blue-600 transition">Características</a>
      <a href="#contact" class="block text-gray-700 hover:text-blue-600 transition">Contacto</a>
      <a href="/admin" class="block text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        Iniciar sesión
      </a>
    </div>
  </header>

  <!-- JS para mostrar/ocultar menú móvil -->
  <script>
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
