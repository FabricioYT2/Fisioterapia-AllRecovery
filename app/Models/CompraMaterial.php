<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompraMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'costo_total',
        'fecha_compra',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
    ];

    /**
     * Detalles compra material.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompraMaterial::class, 'compra_materials_id');
    }

    /**
     * Egreso compra material.
     */
    public function egreso(): HasOne
    {
        return $this->hasOne(IngresoEgreso::class, 'compra_materials_id');
    }
}
