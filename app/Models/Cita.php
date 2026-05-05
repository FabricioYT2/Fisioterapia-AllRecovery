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
        'turno',
        'estado',
        'motivo',
        'fuente',
        'sesiones_totales',
        'sesion_actual',
    ];
    protected $appends = ['sesiones_restantes'];

    public function getSesionesRestantesAttribute(): int
    {
        $total = $this->sesiones_totales ?? 1;
        $actual = $this->sesion_actual ?? 1;
        return max(0, $total - $actual);
    }

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];


    public function cobros(): HasMany
    {
        return $this->hasMany(Cobro::class, 'citas_id');
    }
    
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'pacientes_id');
    }
    
    public function historialClinico(): HasOne
    {
        return $this->hasOne(HistorialClinico::class, 'citas_id');
    }
}
