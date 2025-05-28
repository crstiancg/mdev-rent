<?php

namespace Database\Seeders;

use App\Models\Inventario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Inventario::insert([
            [
                'producto_id' => 1,
                'cantidad_total' => 100,
                'cantidad_disponible' => 100,
                'disponible' => true,
            ],
            [
                'producto_id' => 2,
                'cantidad_total' => 50,
                'cantidad_disponible' => 50,
                'disponible' => true,
            ],
            [
                'producto_id' => 3,
                'cantidad_total' => 75,
                'cantidad_disponible' => 75,
                'disponible' => true,
            ],
        ]);
    }
}
