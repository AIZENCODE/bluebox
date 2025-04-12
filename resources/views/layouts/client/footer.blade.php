<!-- Footer con colores personalizados -->
<footer class="bg-neutral text-dark border-t mt-12">
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-6 md:space-y-0">

        <!-- Logo + Nombre -->
        <div class="flex items-center space-x-2">
          <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 4v16m8-8H4" />
          </svg>
          <span class="text-xl font-bold text-primary">BlueBox</span>
        </div>

        <!-- Enlaces principales -->
        <div class="flex flex-col md:flex-row md:space-x-6 text-dark space-y-2 md:space-y-0">
          <a href="/" class="hover:text-primary transition">Inicio</a>
          <a href="#features" class="hover:text-primary transition">Características</a>
          <a href="#contact" class="hover:text-primary transition">Contacto</a>
          <a href="/login" class="hover:text-primary transition">Iniciar sesión</a>
        </div>

        <!-- Enlaces legales -->
        <div class="flex flex-col md:flex-row md:space-x-6 text-dark/80 text-sm space-y-2 md:space-y-0">
          <a href="/privacy-policy" class="hover:text-primary transition">Política de Privacidad</a>
          <a href="/terms" class="hover:text-primary transition">Términos y Condiciones</a>
        </div>
      </div>

      <div class="mt-8 text-center text-dark/50 text-sm">
        &copy; {{ date('Y') }} BlueBox. Todos los derechos reservados.
      </div>
    </div>
  </footer>
