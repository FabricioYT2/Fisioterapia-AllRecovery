<?php

namespace App\Console\Commands;

use App\Models\Cobro;
use App\Models\IngresoEgreso;
use Illuminate\Console\Command;

class SincronizarCobrosPagados extends Command
{
    protected $signature = 'cobros:sincronizar';
    protected $description = 'Sincroniza cobros pagados con la tabla de ingresos';

    public function handle()
    {
        $cobros = Cobro::where('estado', 'pagado')->get();
        $creados = 0;

        foreach ($cobros as $cobro) {
            $exists = IngresoEgreso::where('cobros_id', $cobro->id)->exists();
            
            if (!$exists) {
                IngresoEgreso::create([
                    'cobros_id' => $cobro->id,
                    'tipo' => 'ingreso',
                    'monto' => $cobro->monto_total,
                    'fecha' => $cobro->fecha_emision ?? now(),
                    'descripcion' => "Pago de cita - {$cobro->cita?->paciente?->nombre} (Cobro #{$cobro->id})",
                ]);
                $creados++;
            }
        }

        $this->info("✅ Sincronización completada: {$creados} ingresos creados.");
        return 0;
    }
}