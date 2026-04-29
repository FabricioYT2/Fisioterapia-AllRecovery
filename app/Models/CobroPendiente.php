<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CobroPendiente extends Model
{
    use HasFactory;

    protected $fillable = [
        'monto_pagado',
        'monto_adeudado',
        'fecha_pago',
        'cobros_id',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
    ];

    /**
     * Cobro pendiente cobro.
     */
    public function cobro(): BelongsTo
    {
        return $this->belongsTo(Cobro::class, 'cobros_id');
    }
}
