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
            'nombre' => 'BlueBox',
            'razon_social' => 'Bluebox S.A.C.',
            'ruc' => '20613432729',
            'telefono_uno' => '936148456',
            'correo_uno' => 'migelo5511@gmail.com',
            'direccion_uno' => 'cinco esquinas',
        ]);
        // Tipo de cuenta
        AccountType::create([
            'nombre' => 'Cuenta corriente',
            'descripcion' => 'Cuenta normal',
        ]);
        AccountType::create([
            'nombre' => 'Cuenta de ahorro',
            'descripcion' => 'Cuenta de ahorro para almacenar',
        ]);
        // Fin tipo de cuenta

        // Bancos
        Bank::create([
            'nombre' => 'BCP',
            'descripcion' => 'Banco Bcp',
        ]);
        Bank::create([
            'nombre' => 'BBVA',
            'descripcion' => 'Banco BBVA',
        ]);
        // Fin Bancos

        // Moneda
        Currency::create([
            'nombre' => 'Soles',
            'descripcion' => 'Moneda Peruana',
            'simbolo' => 'S/',
        ]);
        Currency::create([
            'nombre' => 'Dolar',
            'descripcion' => 'Moneda EEUU',
            'simbolo' => '$',
        ]);
        // Fin moneda

        // igv

        Igv::create([
            'tipo' => 'Sujeto a igv',
            'porcentaje' => 18.00,
        ]);
        Igv::create([
            'tipo' => 'Sin igv',
            'porcentaje' => 00.00,
        ]);

        // Fin igv

        // Cuentas
        Account::create([
            'nombre'              => 'Cuenta Corriente Principal',
            'numero'              => '1234567890',
            'numero_interbancario' => '00212345678901234567',
            'estado'              => true,
            'bank_id'             => 1,
            'accounttype_id'      => 1,
            'currency_id'         => 1,
        ]);
        Account::create([
            'nombre'              => 'Cuenta de detraccion',
            'numero'              => '1234567890',
            'numero_interbancario' => '00212345678901234567',
            'estado'              => true,
            'bank_id'             => 1,
            'accounttype_id'      => 1,
            'currency_id'         => 1,
        ]);
        // Fin de cuentas

        // inicio compania
        Companie::create([
            'nombre'        => 'Tech Solutions S.A.C.',
            'razon_social'  => 'Tech Solutions Sociedad Anónima Cerrada',
            'ruc'           => '20123456789',
            'correo'        => 'contacto@techsolutions.com',
            'telefono'      => '012345678',
            'direccion'     => 'Av. Innovación 123, Lima',
            'estado'        => true, // true para activo, false para inactivo
        ]);

        Companie::create([
            'nombre'        => 'AGROINDUSTRIA CASABLANCA',
            'razon_social'  => 'AGROINDUSTRIA CASABLANCA S.A.C.',
            'ruc'           => '20452302951',
            'correo'        => 'casabalnca@gmail.com',
            'telefono'      => '012345678',
            'direccion'     => 'Nro. S/n Fnd. la Calera',
            'estado'        => true, // true para activo, false para inactivo
        ]);
        // Fin compania

        // Servicios
        Service::create([
            'nombre'       => 'Consultoría de Marketing',
            'descripcion'  => 'Asesoría especializada en estrategias de marketing digital.',
            'precio'   => 1000.00,
            'precio_min'   => 500.00,
            'precio_max'   => 1500.00,

        ]);

        Service::create([
            'nombre'       => 'Desarrollo Web',
            'descripcion'  => 'Creación de sitios web personalizados adaptados a las necesidades del cliente.',
            'precio'   => 1000.00,
            'precio_min'   => 1200.00,
            'precio_max'   => 3000.00,
            'estado'       => true,
        ]);
        // Fin Servicios

        // Productos

        Product::create([
            'nombre'       => 'Laptop Lenovo ThinkPad',
            'descripcion'  => 'Laptop empresarial con procesador Intel Core i7, 16GB RAM y 512GB SSD.',
            'precio'   => 1000.00,
            'precio_min'   => 3200.00,
            'precio_max'   => 4200.00,
            'estado'       => true,
        ]);

        Product::create([
            'nombre'       => 'Monitor LG 24"',
            'descripcion'  => 'Monitor LED Full HD de 24 pulgadas, ideal para trabajo y entretenimiento.',
            'precio'   => 1000.00,
            'precio_min'   => 550.00,
            'precio_max'   => 700.00,
            'estado'       => true,
        ]);

        // Fin productos

        // clientes
        $client = Client::create([
            'nombre'        => 'Juan Pérez',
            'correo'        => 'juan.perez@example.com',
            'telefono_uno'  => '987654321',
            'telefono_dos'  => '912345678',
            'estado'        => true,
        ]);

        // Asociar con compañías
        $client->companies()->sync([1, 2]); // IDs de las compañías relacionadas
        // Fin clientes

        // Tag 

        Category::create(['name' => 'Tecnología', 'slug' => 'tecnologia']);
        Category::create(['name' => 'Salud', 'slug' => 'salud']);

        Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        Tag::create(['name' => 'Inteligencia Artificial', 'slug' => 'inteligencia-artificial']);
        // Fin tag

        // Posts 

        Post::create([
            'slug' => 'como-mejorar-tu-productividad',
            'title' => 'Cómo mejorar tu productividad en el trabajo',
            'excerpt' => 'Consejos prácticos para organizar mejor tu tiempo, mantener la concentración y ser más eficiente día a día.',
            'category_id' => 1,
            'author_id' => 1
        ]);

        Post::create([
            'slug' => 'tendencias-tecnologicas-2025',
            'title' => 'Tendencias tecnológicas que marcarán el 2025',
            'excerpt' => 'Desde inteligencia artificial hasta avances en biotecnología, descubre qué tecnologías dominarán este año.',
            'category_id' => 1,
            'author_id' => 1
        ]);

        Post::create([
            'slug' => 'guia-wordpress-principiantes',
            'title' => 'Guía completa de WordPress para principiantes',
            'excerpt' => 'Aprende a crear y administrar tu sitio web con WordPress paso a paso, sin necesidad de conocimientos previos.',
            'category_id' => 1,
            'author_id' => 1
        ]);

        Post::create([
            'slug' => 'beneficios-del-ejercicio-diario',
            'title' => '5 beneficios del ejercicio diario para tu salud',
            'excerpt' => 'Mejora tu estado físico, mental y emocional con solo 30 minutos de ejercicio al día. Te explicamos por qué.',
            'category_id' => 1,
            'author_id' => 1
        ]);

        Post::create([
            'slug' => 'mejores-lenguajes-programacion-actualidad',
            'title' => 'Los mejores lenguajes de programación en la actualidad',
            'excerpt' => 'Una revisión de los lenguajes más demandados por empresas y desarrolladores en 2025.',
            'category_id' => 1,
            'author_id' => 1
        ]);


        // Fin posts

    }
}
