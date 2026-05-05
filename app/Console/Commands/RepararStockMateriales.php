<?php

namespace App\Console\Commands;

use App\Models\Material;
use App\Models\CompraMaterial;
use App\Models\HistorialMaterial;
use Illuminate\Console\Command;

class RepararStockMateriales extends Command
{
    protected $signature = 'stock:reparar';
    protected $description = 'Recalcula el stock real basado en compras y usos históricos';

    public function handle()
    {
        $this->info('🔄 Reseteando stock a 0...');
        Material::query()->update(['stock_actual' => 0]);

        $this->info('📦 Sumando stock de compras...');
        $compras = CompraMaterial::with('detalleCompraMateriales')->get();
        foreach ($compras as $compra) {
            foreach ($compra->detalleCompraMateriales as $detalle) {
                $mat = Material::find($detalle->materiales_id);
                if ($mat) $mat->increment('stock_actual', $detalle->cantidad);
            }
        }

        $this->info('📉 Restando stock usado en historiales...');
        $usos = HistorialMaterial::all();
        foreach ($usos as $uso) {
            $mat = Material::find($uso->materiales_id);
            if ($mat) $mat->decrement('stock_actual', $uso->cantidad_usada);
        }

        $this->info('✅ Stock reparado correctamente.');
        return 0;
    }
}