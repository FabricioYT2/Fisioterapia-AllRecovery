<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cobro extends Model
{
    use HasFactory;

    protected $fillable = [
        'citas_id',
        'monto_total',
        'fecha_emision',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    /**
     * Cita cobro.
     */
    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'citas_id');
    }

    /**
     * Pagos pendientes cobro.
     */
    public function pagosPendientes(): HasMany
    {
        return $this->hasMany(CobroPendiente::class, 'cobros_id');
    }

    /**
     * Ingreso cobro.
     */
    public function ingreso(): HasOne
    {
        return $this->hasOne(IngresoEgreso::class, 'cobros_id');
    }
}
