<?php

namespace App\Observers;

use App\Models\CompraMaterial;
use App\Models\IngresoEgreso;

class CompraMaterialObserver
{
    public function created(CompraMaterial $compra)
    {
        $this->crearEgreso($compra);
    }

    public function updated(CompraMaterial $compra)
    {
        if ($compra->isDirty('costo_total')) {
            $this->actualizarEgreso($compra);
        }
    }

    public function deleted(CompraMaterial $compra)
    {
        IngresoEgreso::where('compra_materiales_id', $compra->id)->delete();
    }

    private function crearEgreso(CompraMaterial $compra)
    {
        $exists = IngresoEgreso::where('compra_materiales_id', $compra->id)->exists();
        if (!$exists) {
            IngresoEgreso::create([
                'compra_materiales_id' => $compra->id,
                'tipo' => 'egreso',
                'monto' => (float) $compra->costo_total,
                'fecha' => $compra->fecha_compra ?? now(),
                'descripcion' => 'Compra de materiales (Compra #' . $compra->id . ')',
            ]);
        }
    }

    private function actualizarEgreso(CompraMaterial $compra)
    {
        $egreso = IngresoEgreso::where('compra_materiales_id', $compra->id)->first();
        if ($egreso) {
            $egreso->update(['monto' => (float) $compra->costo_total]);
        }
    }
}