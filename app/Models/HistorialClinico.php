<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class HistorialClinico extends Model
{
    use HasFactory;

    protected $fillable = [
        'citas_id',
        'codigo_acceso',
        'evaluacion',
        'recomendaciones',
        'recetas_compra',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($historial) {
            if (empty($historial->codigo_acceso)) {
                $historial->codigo_acceso = strtoupper(Str::random(8));
            }
        });
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'citas_id');
    }

    public function materialesUsados(): HasMany
    {
        return $this->hasMany(HistorialMaterial::class, 'historial_clinicos_id');
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'historial_clinicos_id');
    }
    public function ejerciciosRecomendados()
    {
        return $this->hasMany(HistorialEjercicio::class, 'historial_clinicos_id')
            ->with('ejercicio');
    }
}