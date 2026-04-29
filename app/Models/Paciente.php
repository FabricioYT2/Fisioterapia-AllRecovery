<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paciente extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ci',
        'edad',
        'telefono',
        'email',
        'actividad_fisica',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Citas del paciente.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'pacientes_id');
    }

    /**
     * Ejercicios del paciente.
     */
    public function ejercicios(): BelongsToMany
    {
        return $this->belongsToMany(Ejercicio::class, 'paciente_ejercicios', 'pacientes_id', 'ejercicios_id')
            ->withPivot('fecha_asignacion', 'series', 'repeticiones', 'frecuencia')
            ->withTimestamps();
    }
}
