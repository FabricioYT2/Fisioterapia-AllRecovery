<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PacienteEjercicio extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paciente_ejercicios';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'pacientes_id',
        'ejercicios_id',
        'fecha_asignacion',
        'series',
        'repeticiones',
        'frecuencia',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
    ];

    /**
     * Paciente paciente ejercicio.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id');
    }

    /**
     * Ejercicio paciente ejercicio.
     */
    public function ejercicio(): BelongsTo
    {
        return $this->belongsTo(Ejercicio::class, 'ejercicios_id');
    }
}