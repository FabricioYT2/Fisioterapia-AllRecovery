<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'historial_clinicos_id',
        'nombre_servicio',
        'precio',
        'duracion_minutos',
    ];

    /**
     * Historial clinico servicio.
     */
    public function historialClinico(): BelongsTo
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinicos_id');
    }
}
