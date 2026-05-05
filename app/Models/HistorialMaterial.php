<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialMaterial extends Model
{
    use HasFactory;

    protected $table = 'historial_materiales';

    protected $fillable = [
        'historial_clinicos_id',
        'materiales_id',
        'cantidad_usada',
    ];
    public function historialClinico(): BelongsTo
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_clinicos_id');
    }
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'materiales_id');
    }
}
