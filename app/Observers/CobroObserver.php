<?php

namespace App\Observers;

use App\Models\Cobro;
use App\Models\IngresoEgreso;

class CobroObserver
{
    public function created(Cobro $cobro)
    {
        if ($cobro->estado === 'pagado') {
            $this->sincronizarIngreso($cobro);
        }
    }

    public function updated(Cobro $cobro)
    {
        if ($cobro->isDirty('estado') && $cobro->estado === 'pagado') {
            $this->sincronizarIngreso($cobro);
        }

        if ($cobro->estado === 'pagado' && $cobro->isDirty('monto_total')) {
            $this->actualizarIngreso($cobro);
        }
    }

    public function deleted(Cobro $cobro)
    {

        IngresoEgreso::where('cobros_id', $cobro->id)->delete();
    }

    private function sincronizarIngreso(Cobro $cobro)
    {

        $exists = IngresoEgreso::where('cobros_id', $cobro->id)->exists();
        
        if (!$exists) {
            IngresoEgreso::create([
                'cobros_id' => $cobro->id,
                'tipo' => 'ingreso',
                'monto' => $cobro->monto_total,
                'fecha' => $cobro->fecha_emision ?? now(),
                'descripcion' => "Pago de cita - {$cobro->cita?->paciente?->nombre} (Cobro #{$cobro->id})",
            ]);
        }
    }

    private function actualizarIngreso(Cobro $cobro)
    {
        $ingreso = IngresoEgreso::where('cobros_id', $cobro->id)->first();
        
        if ($ingreso) {
            $ingreso->update([
                'monto' => $cobro->monto_total,
            ]);
        }
    }
}