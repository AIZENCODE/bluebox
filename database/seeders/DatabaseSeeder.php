<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Data;
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

        Bank::create([
            'nombre' => 'BCP',
            'descripcion' => 'Banco Bcp',
        ]);
        Bank::create([
            'nombre' => 'BBVA',
            'descripcion' => 'Banco BBVA',
        ]);
        
    }
}
