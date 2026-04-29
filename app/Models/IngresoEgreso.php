<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngresoEgreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'monto',
        'fecha',
        'descripcion',
        'cobros_id',
        'compra_materiales_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Cobro ingreso/egreso.
     */
    public function cobro(): BelongsTo
    {
        return $this->belongsTo(Cobro::class, 'cobros_id');
    }

    /**
     * Compra material ingreso/egreso.
     */
    public function compraMaterial(): BelongsTo
    {
        return $this->belongsTo(CompraMaterial::class, 'compra_materiales_id');
    }
}
