<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'producto_id',
        'cantidad_total',
        'cantidad_disponible',
        'disponible'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function alquilerdets()
    {
        return $this->hasMany(Alquilerdet::class);
    }

}
