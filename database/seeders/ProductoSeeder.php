<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        Producto::insert([
            [
                'nombre' => 'Producto 1',
                'descripcion' => 'Descripción del Producto 1',
                'precio_venta' => 100.00,
                'precio_alquiler' => 10.00,
                'categoria_id' => 1,
                'imagen' => $faker->imageUrl(640, 480),
            ],
            [
                'nombre' => 'Producto 2',
                'descripcion' => 'Descripción del Producto 2',
                'precio_venta' => 200.00,
                'precio_alquiler' => 20.00,
                'categoria_id' => 2,
                'imagen' => $faker->imageUrl(640, 480),
            ],
            [
                'nombre' => 'Producto 3',
                'descripcion' => 'Descripción del Producto 3',
                'precio_venta' => 300.00,
                'precio_alquiler' => 30.00,
                'categoria_id' => 3,
                'imagen' => $faker->imageUrl(640, 480),
            ],
        ]);
    }
}
