<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_venta',
        'precio_alquiler',
        'categoria_id',
        'imagen',
        'estado',
        'disponible'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
