<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'historial_clinicos_id',
        'materials_id',
        'cantidad_usada',
    ];

    /**
     * Historial clinico historial material.
     */
    public function historialClinico(): BelongsTo
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinicos_id');
    }

    /**
     * Material historial material.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'materials_id');
    }
}
