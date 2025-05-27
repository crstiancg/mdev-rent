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

}
