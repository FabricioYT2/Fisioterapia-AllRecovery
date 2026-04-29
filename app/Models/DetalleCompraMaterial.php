<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompraMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtotal',
        'cantidad',
        'precio_unitario',
        'materiales_id',
        'compra_materiales_id',
    ];

    /**
     * Compra material detalle.
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(CompraMaterial::class, 'compra_materiales_id');
    }

    /**
     * Material detalle.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'materiales_id');
    }
}
