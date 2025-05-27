<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::insert([
            ['name' => 'Luces'],
            ['name' => 'Autoctonos'],
            ['name' => 'Mestizos'],
            ['name' => 'Botargas'],
            ['name' => 'Disfraces por tematica'],
        ]);
    }
}
