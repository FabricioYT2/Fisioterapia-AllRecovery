<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEjercicio extends Model
{
    protected $table = 'historial_ejercicios';
    
    protected $fillable = [
        'historial_clinicos_id',
        'ejercicios_id',
        'series',
        'repeticiones',
        'frecuencia',
        'notas_adicionales',
        'fecha_asignacion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
    ];

    public function historial(): BelongsTo
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinicos_id');
    }

    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicios_id');
    }
}