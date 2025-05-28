<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alquilerdet extends Model
{
    protected $fillable = [
        'alquiler_id',
        'inventario_id',
        'cantidad',
        'precio_alquiler',
        'subtotal'
    ];

    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
    
    protected static function booted()
    {
        static::deleting(function ($detalle) {
            if ($detalle->inventario) {
                if($detalle->inventario->cantidad_total < $detalle->cantidad) {
                    throw new \Exception('No se puede eliminar el detalle porque la cantidad es mayor a la cantidad total del inventario.');
                }
                $detalle->inventario->cantidad_disponible += $detalle->cantidad;
                $detalle->inventario->disponible = true;
                $detalle->inventario->save();
            }
        });
    }
}
