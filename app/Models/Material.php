<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'stock_actual',
        'stock_minimo',
        'unidad',
    ];

    /**
     * Detalles compra material.
     */
    public function detallesCompra(): HasMany
    {
        return $this->hasMany(DetalleCompraMaterial::class, 'materiales_id');
    }

    /**
     * Historiales uso material.
     */
    public function historialesUso(): HasMany
    {
        return $this->hasMany(HistorialMaterial::class, 'materiales_id');
    }
}
