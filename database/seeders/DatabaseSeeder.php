<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Client;
use App\Models\Companie;
use App\Models\Currency;
use App\Models\Data;
use App\Models\Exchange;
use App\Models\Igv;
use App\Models\Post;
use App\Models\Product;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([

            'name' => 'Diego Miguel Saravia',
            'email' => 'migelo5511@gmail.com',
            'password' => bcrypt('123456789'),
        ]);

        Data::create([
            'name' => 'BlueBox',
            'company_name' => 'Bluebox S.A.C.',
            'ruc' => '20613432729',
            'phone_one' => '936148456',
            'email_one' => 'migelo5511@gmail.com',
            'address_one' => 'cinco esquinas',
        ]);
        // type de cuenta
        AccountType::insert([
            [
                'name' => 'Cuenta corriente',
                'description' => 'Cuenta normal',
            ],
            [
                'name' => 'Cuenta de ahorro',
                'description' => 'Cuenta de ahorro para almacenar',
            ],
        ]);
        // Fin type de cuenta

        // Bancos
        Bank::insert([
            [
                'name' => 'Interbank',
                'description' => 'Banco Interbank',
            ],
            [
                'name' => 'Scotiabank',
                'description' => 'Banco Scotiabank',
            ],
            [
                'name' => 'BBVA',
                'description' => 'Banco BBVA Continental',
            ],
        ]);
        // Fin Bancos

        // Moneda
        Currency::insert([
            [
                'name' => 'Dolar',
                'description' => 'Moneda EEUU',
                'symbol' => '$',
            ],
            [
                'name' => 'Soles',
                'description' => 'Moneda Peruana',
                'symbol' => 'S/',
            ],
        ]);
        // Fin moneda

        // igv
        Igv::insert([
            [
                'type' => 'Sujeto a igv',
                'percentage' => 18.00,
            ],
            [
                'type' => 'Sin igv',
                'percentage' => 00.00,
            ],
        ]);
        // Fin igv

        // Cuentas
        Account::insert([
            [
                'name'              => 'Cuenta Corriente Principal',
                'number'              => '1234567890',
                'interbank_number' => '00212345678901234567',
                'state'              => true,
                'bank_id'             => 1,
                'accounttype_id'      => 1,
                'currency_id'         => 1,
            ],
            [
                'name'              => 'Cuenta de detraccion',
                'number'              => '1234567890',
                'interbank_number' => '00212345678901234567',
                'state'              => true,
                'bank_id'             => 1,
                'accounttype_id'      => 1,
                'currency_id'         => 1,
            ],

        ]);
        // Fin de cuentas

        // Servicios
        Service::insert([
            [
                'name'       => 'Consultoría de Marketing',
                'description'  => 'Asesoría especializada en estrategias de marketing digital.',
                'price'   => 1000.00,
                'price_min'   => 500.00,
                'price_max'   => 1500.00,
            ],
            [
                'name'       => 'Desarrollo Web',
                'description'  => 'Creación de sitios web personalizados adaptados a las necesidades del cliente.',
                'price'   => 1000.00,
                'price_min'   => 1200.00,
                'price_max'   => 3000.00,
            ]


        ]);
        // Fin Servicios

        // Productos
        Product::insert([
            [
                'name'       => 'Laptop Lenovo ThinkPad',
                'description'  => 'Laptop empresarial con procesador Intel Core i7, 16GB RAM y 512GB SSD.',
                'price'   => 1000.00,
                'price_min'   => 3200.00,
                'price_max'   => 4200.00,
            ],
            [
                'name'       => 'Smartphone Samsung Galaxy S21',
                'description'  => 'Teléfono inteligente con pantalla AMOLED de 6.2", cámara triple y 128GB de almacenamiento.',
                'price'   => 1000.00,
                'price_min'   => 1500.00,
                'price_max'   => 2000.00,
            ],
            [
                'name'       => 'Impresora HP LaserJet Pro',
                'description'  => 'Impresora láser monocromática, ideal para oficinas pequeñas.',
                'price'   => 1000.00,
                'price_min'   => 300.00,
                'price_max'   => 500.00,
            ]

        ]);
        // Fin productos

        // Inicio compania
        Companie::insert([
            [
                'name'        => 'Tech Solutions S.A.C.',
                'company_name'  => 'Tech Solutions Sociedad Anónima Cerrada',
                'ruc'           => '20123456789',
                'mail'        => 'bluebox.ccruces@gmail.com',
                'phone'      => '012345678',
                'address'     => 'Av. Innovación 123, Lima',
                'state'        => true, // true para activo, false para inactivo
            ],
            [
                'name'        => 'AGROINDUSTRIA CASABLANCA',
                'company_name'  => 'AGROINDUSTRIA CASABLANCA S.A.C.',
                'ruc'           => '20452302951',
                'mail'        => 'aizencode@gmail.com',
                'phone'      => '012345678',
                'address'     => 'Nro. S/n Fnd. la Calera',
                'state'        => true, // true para activo, false para inactivo
            ]

        ]);
        // Fin compania

        // clientes
        $client = Client::create([
            'name'        => 'Juan Pérez',
            'mail'        => 'migelo5511@gmail.com',
            'phone_one'  => '987654321',
            'phone_two'  => '912345678',
            'state'        => true,
        ]);
        // Asociar con compañías
        $client->companies()->sync([1, 2]); // IDs de las compañías relacionadas
        // Fin clientes


        // Categoria
        Category::insert([
            [
                'name' => 'Desarrollo Web',
                'slug' => 'desarrollo-web'
            ],
            [
                'name' => 'Marketing Digital',
                'slug' => 'marketing-digital'
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce'
            ],
            [
                'name' => 'Diseño Gráfico',
                'slug' => 'diseno-grafico'
            ],
        ]);
        // Fin categoria

        // Tag
        Tag::insert([
            [
                'name' => 'JavaScript',
                'slug' => 'javascript'
            ],
            [
                'name' => 'PHP',
                'slug' => 'php'
            ],
            [
                'name' => 'HTML',
                'slug' => 'html'
            ],
            [
                'name' => 'CSS',
                'slug' => 'css'
            ],
            [
                'name' => 'React',
                'slug' => 'react'
            ],
            [
                'name' => 'Vue.js',
                'slug' => 'vuejs'
            ],
            [
                'name' => 'Node.js',
                'slug' => 'nodejs'
            ],
        ]);
        // Fin tag

        // Posts
        Post::insert([
            [
                'slug' => 'introduccion-a-html-css',
                'title' => 'Introducción a HTML y CSS para principiantes',
                'excerpt' => 'Aprende los fundamentos de HTML y CSS para crear tus primeras páginas web.',
                'category_id' => 1,
                'author_id' => 1
            ],

            [
                'slug' => 'mejores-practicas-seo',
                'title' => 'Mejores prácticas de SEO en 2025',
                'excerpt' => 'Descubre las estrategias más efectivas para mejorar el posicionamiento de tu sitio web en los motores de búsqueda.',
                'category_id' => 1,
                'author_id' => 1
            ],
            [
                'slug' => 'guia-marketing-redes-sociales',
                'title' => 'Guía de marketing en redes sociales para empresas',
                'excerpt' => 'Aprende a utilizar las redes sociales para impulsar tu negocio y conectar con tu audiencia.',
                'category_id' => 1,
                'author_id' => 1
            ],

            [
                'slug' => 'tendencias-diseno-grafico-2025',
                'title' => 'Tendencias de diseño gráfico para el 2025',
                'excerpt' => 'Explora las tendencias más innovadoras en diseño gráfico que marcarán el año.',
                'category_id' => 1,
                'author_id' => 1
            ],

            [
                'slug' => 'como-crear-un-ecommerce-exitoso',
                'title' => 'Cómo crear un e-commerce exitoso desde cero',
                'excerpt' => 'Guía paso a paso para lanzar tu tienda online y atraer clientes.',
                'category_id' => 1,
                'author_id' => 1
            ],
            [
                'slug' => 'mejores-practicas-seguridad-web',
                'title' => 'Mejores prácticas de seguridad web en 2025',
                'excerpt' => 'Asegura tu sitio web con estas recomendaciones de seguridad actualizadas.',
                'category_id' => 1,
                'author_id' => 1
            ],

            [
                'slug' => 'como-optimizar-tu-sitio-web',
                'title' => 'Cómo optimizar tu sitio web para mejorar la velocidad',
                'excerpt' => 'Consejos y herramientas para hacer que tu sitio web cargue más rápido.',
                'category_id' => 1,
                'author_id' => 1
            ],
            [
                'slug' => 'estrategias-email-marketing-2025',
                'title' => 'Estrategias de email marketing para 2025',
                'excerpt' => 'Descubre cómo mejorar tus campañas de email marketing y aumentar tu tasa de conversión.',
                'category_id' => 1,
                'author_id' => 1
            ],

            [
                'slug' => 'como-usar-google-analytics',
                'title' => 'Cómo usar Google Analytics para analizar tu tráfico web',
                'excerpt' => 'Aprende a interpretar los datos de Google Analytics y optimiza tu estrategia digital.',
                'category_id' => 1,
                'author_id' => 1
            ],

        ]);
        // Fin posts


        // Tipo de cambio

        Exchange::insert([
            [
                'from_currency_id' => 1,
                'to_currency_id' => 2,
                'rate' => 3.50,
                'date' => now(), // Hoy
            ],
            [
                'from_currency_id' => 1,
                'to_currency_id' => 2,
                'rate' => 3.48,
                'date' => now()->subDay(), // 1 día antes
            ],
            [
                'from_currency_id' => 1,
                'to_currency_id' => 2,
                'rate' => 3.45,
                'date' => now()->subDays(2), // 2 días antes
            ],
            [
                'from_currency_id' => 1,
                'to_currency_id' => 2,
                'rate' => 3.43,
                'date' => now()->subDays(3), // 3 días antes
            ],
        ]);

        // Fin tipo de cambio

    }
}
