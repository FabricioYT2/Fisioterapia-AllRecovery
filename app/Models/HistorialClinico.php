<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistorialClinico extends Model
{
    use HasFactory;

    protected $fillable = [
        'citas_id',
        'evaluacion',
        'recomendaciones',
        'recetas_compra',
    ];

    /**
     * Cita historial clinico.
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'citas_id');
    }

    /**
     * Materiales usados historial clinico.
     */
    public function materialesUsados(): HasMany
    {
        return $this->hasMany(HistorialMaterial::class, 'historial_clinicos_id');
    }

    /**
     * Servicios historial clinico.
     */
    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'historial_clinicos_id');
    }
}
