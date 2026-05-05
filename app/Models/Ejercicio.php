<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ejercicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_ejercicio',
        'descripcion',
        'video_url',
    ];

    /**
     * Pacientes ejercicio.
     */
    public function pacientes(): BelongsToMany
    {
        return $this->belongsToMany(Paciente::class, 'paciente_ejercicios', 'ejercicios_id', 'pacientes_id')
            ->using(PacienteEjercicio::class)
            ->withPivot('fecha_asignacion', 'series', 'repeticiones', 'frecuencia')
            ->withTimestamps();
    }
}