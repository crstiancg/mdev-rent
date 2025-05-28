<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alquiler extends Model
{
    protected $fillable = [
        'cliente_id',
        'fecha_alquiler',
        'fecha_entrega',
        'fecha_devolucion',
        'monto_total',
        'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function alquilerDetalles()
    {
        return $this->hasMany(Alquilerdet::class);
    }

    protected static function booted()
    {
        static::saved(function ($alquiler) {
            foreach ($alquiler->alquilerDetalles as $detalle) {
                $inventario = $detalle->inventario;

                if ($inventario && $inventario->cantidad_disponible >= $detalle->cantidad) {
                    $inventario->cantidad_disponible -= $detalle->cantidad;

                    if ($inventario->cantidad_disponible <= 0) {
                        $inventario->disponible = false;
                    }

                    $inventario->save();
                }
            }
        });

        static::updating(function ($alquiler) {
        foreach ($alquiler->getOriginal('alquilerDetalles') ?? [] as $detalleOriginal) {
            $detalle = \App\Models\Alquilerdet::find($detalleOriginal['id']);
            if ($detalle && $detalle->inventario) {
                $detalle->inventario->cantidad_disponible += $detalle->cantidad;
                $detalle->inventario->disponible = true;
                $detalle->inventario->save();
            }
        }
        });
    }

//     protected static function booted()
// {
//     static::saved(function ($alquiler) {
//         $detallesAgrupados = $alquiler->alquilerDetalles
//             ->groupBy('inventario_id')
//             ->map(function ($items) {
//                 return $items->sum('cantidad');
//             });

//         foreach ($detallesAgrupados as $inventarioId => $cantidadTotal) {
//             $inventario = \App\Models\Inventario::find($inventarioId);

//             if ($inventario && $inventario->cantidad_disponible >= $cantidadTotal) {
//                 $inventario->cantidad_disponible -= $cantidadTotal;

//                 if ($inventario->cantidad_disponible <= 0) {
//                     $inventario->disponible = false;
//                 }

//                 $inventario->save();
//             }
//         }
//     });
// }

}
