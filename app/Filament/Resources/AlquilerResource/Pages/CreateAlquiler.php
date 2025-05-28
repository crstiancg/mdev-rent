<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use App\Models\Cliente;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

//    protected function handleRecordCreation(array $data): Model
//     {
//         // \dd($data); // Verifica que el array contiene los campos necesarios
//          return DB::transaction(function () use ($data) {

//             $alquiler = \App\Models\Alquiler::create([
//                 'cliente_id' => $data['cliente_id'],
//                 'fecha_alquiler' => $data['fecha_alquiler'],
//                 'fecha_entrega' => $data['fecha_entrega'],
//                 'fecha_devolucion' => $data['fecha_devolucion'],
//                 'monto_total' => $data['monto_total'],
//                 'estado' => $data['estado'],
//             ]);

//             foreach ($data['alquilerDetalles'] as $detalle) {
//                 $inventario = \App\Models\Inventario::find($detalle['inventario_id']);

//                 if (!$inventario || $inventario->cantidad_disponible < $detalle['cantidad']) {
//                     throw new \Exception("Stock insuficiente para el producto ID {$detalle['inventario_id']}");
//                 }

//                 $alquiler->alquilerDetalles()->create($detalle); // 👈 este campo debe incluir inventario_id correctamente

//                 $inventario->cantidad_disponible -= $detalle['cantidad'];
//                 $inventario->disponible = $inventario->cantidad_disponible > 0;
//                 $inventario->save();
//             }

//             return $alquiler;
//         });

//         // return DB::transaction(function () use ($data) {
//         //     // Crear el alquiler
//         //     $alquiler = \App\Models\Alquiler::create([
//         //         'cliente_id' => $data['cliente_id'],
//         //         'fecha_alquiler' => $data['fecha_alquiler'],
//         //         'fecha_entrega' => $data['fecha_entrega'],
//         //         'fecha_devolucion' => $data['fecha_devolucion'],
//         //         'monto_total' => $data['monto_total'],
//         //         'estado' => $data['estado'],
//         //     ]);

//         //     foreach ($data['alquilerDetalles'] as $detalle) {
//         //         $inventario = \App\Models\Inventario::find($detalle['inventario_id']);

//         //         if (!$inventario || $inventario->cantidad_disponible < $detalle['cantidad']) {
//         //             throw new \Exception("Stock insuficiente para el producto ID {$detalle['inventario_id']}");
//         //         }

//         //         // Crear el detalle
//         //         $alquiler->alquilerDetalles()->create($detalle);

//         //         // Descontar inventario
//         //         $inventario->cantidad_disponible -= $detalle['cantidad'];
//         //         if ($inventario->cantidad_disponible <= 0) {
//         //             $inventario->disponible = false;
//         //         }
//         //         $inventario->save();
//         //     }

//         //     return $alquiler;
//         // });
//     }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data);
    }

    protected function afterValidate(): void
    {
        $agrupados = collect($this->data['alquilerDetalles'] ?? [])
            ->groupBy('inventario_id')
            ->map(fn ($items) => $items->sum('cantidad'));

        foreach ($agrupados as $inventarioId => $cantidadTotal) {
            $inventario = \App\Models\Inventario::find($inventarioId);
            $disponible = $inventario?->cantidad_disponible ?? 0;

            if ($cantidadTotal > $disponible) {
                $this->addError(
                    'alquilerDetalles',
                    "El producto \"{$inventario->producto->nombre}\" excede el stock disponible de {$disponible}."
                );
            }
        }
    }

    // protected function afterValidate(): void
    // {
    //     $this->data['alquilerDetalles'] ??= [];

    //     $agrupados = [];

    //     foreach ($this->data['alquilerDetalles'] as $detalle) {
    //         $inventarioId = $detalle['inventario_id'] ?? null;
    //         $cantidad = (int) ($detalle['cantidad'] ?? 0);

    //         if (!$inventarioId) continue;

    //         $agrupados[$inventarioId] = ($agrupados[$inventarioId] ?? 0) + $cantidad;
    //     }

    //     foreach ($agrupados as $inventarioId => $cantidadTotal) {
    //         $inventario = \App\Models\Inventario::find($inventarioId);
    //         $disponible = $inventario?->cantidad_disponible ?? 0;

    //         if ($cantidadTotal > $disponible) {
    //             Validator::make([], [])->after(function ($validator) use ($inventario, $disponible) {
    //                 $validator->errors()->add(
    //                     'alquilerDetalles',
    //                     'La cantidad total para el producto "' . ($inventario->producto->nombre ?? 'N/A') . '" excede el stock disponible (' . $disponible . ').'
    //                 );
    //             })->validate();
    //         }
    //     }
    // }

    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Guardar')
                ->icon('heroicon-o-check')
                ->color('success')
                , // renombrar "Create"
            Actions\CreateAction::make('createAnother')
                ->label('Guardar y nuevo')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->createAnother(true),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->icon('heroicon-o-x-mark')
                ->url($this->getResource()::getUrl('index'))
                ->color('warning'),
        ];
    }

    

    

}
