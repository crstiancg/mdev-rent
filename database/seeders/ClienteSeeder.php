<?php

namespace Database\Seeders;

use App\Models\Cliente;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::insert([
            [
                'nombre' => 'CRISTIAN',
                'correo' => 'btzarmin@gmail.com',
                'dni' => '74415030',
                'apellido_paterno' => 'CONDORI',
                'apellido_materno' => 'GUZMAN',
                'celular' => '987654321',
            ]
        ]);
    }
}
