<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use App\Models\Cliente;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAlquiler extends CreateRecord
{
    protected static string $resource = AlquilerResource::class;

//     public function mutateFormDataBeforeCreate(array $data): array
// {
//     // dd($data); // Verifica que el array contiene los campos necesarios

//     $cliente = Cliente::updateOrCreate(
//         ['dni' => $data['dni']],
//         [
//             'nombre' => $data['nombre'],
//             'apellido_paterno' => $data['apellido_paterno'],
//             'apellido_materno' => $data['apellido_materno'],
//             'celular' => $data['celular'],
//             'correo' => $data['correo'],
//         ]
//     );

//     $data['cliente_id'] = $cliente->id;

//     unset(
//         $data['dni'],
//         $data['nombre'],
//         $data['apellido_paterno'],
//         $data['apellido_materno'],
//         $data['celular'],
//         $data['correo']
//     );

//     return $data;
// }
}
