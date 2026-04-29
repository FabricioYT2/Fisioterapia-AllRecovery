<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'pacientes_id',
        'fecha_hora',
        'estado',
        'motivo',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    /**
     * Paciente cita.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id');
    }

    /**
     * Historial clinico cita.
     */
    public function historialClinico(): HasOne
    {
        return $this->hasOne(HistorialClinico::class, 'citas_id');
    }

    /**
     * Cobros cita.
     */
    public function cobros(): HasMany
    {
        return $this->hasMany(Cobro::class, 'citas_id');
    }
}
