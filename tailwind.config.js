import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#0d3b66',
                    50: '#e6f0f8',
                    100: '#cce0f1',
                    200: '#99c2e3',
                    300: '#66a3d6',
                    400: '#3385c8',
                    500: '#0d3b66', // color principal
                    600: '#0b345c',
                    700: '#092b4d',
                    800: '#07223e',
                    900: '#051930',
                },
                // Puedes definir otros colores personalizados aquí
                secondary: '#BA0C2F',      // Rojo
                accent: '#FFCE00',         // Amarillo
                neutral: '#F4F4F4',        // Gris claro
                dark: '#1a1a1a',           // Gris oscuro
            },
        },
    },
    plugins: [],
};
