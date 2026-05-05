<?php

namespace App\Console\Commands;

use App\Models\Material;
use App\Models\CompraMaterial;
use App\Models\HistorialClinico;
use App\Models\IngresoEgreso;
use Illuminate\Console\Command;

class RecalcularStockYBalance extends Command
{
    protected $signature = 'sistema:recalcular';
    protected $description = 'Recalcula stock de materiales y balance financiero desde cero';

    public function handle()
    {
        $this->info('🔄 Recalculando stock de materiales...');
        Material::query()->update(['stock_actual' => 0]);
        $compras = CompraMaterial::with('detalleCompraMateriales')->get();
        foreach ($compras as $compra) {
            foreach ($compra->detalleCompraMateriales as $detalle) {
                $material = Material::find($detalle->materiales_id);
                if ($material) {
                    $material->stock_actual += $detalle->cantidad;
                    $material->save();
                }
            }
        }
        
        $this->info('✅ Stock de compras agregado: ' . $compras->count() . ' compras procesadas');
        $historiales = HistorialClinico::with('materialesUsados')->get();
        foreach ($historiales as $historial) {
            foreach ($historial->materialesUsados as $materialUsado) {
                $material = Material::find($materialUsado->materiales_id);
                if ($material) {
                    $material->stock_actual -= $materialUsado->cantidad_usada;
                    $material->save();
                }
            }
        }
        
        $this->info('✅ Stock usado descontado: ' . $historiales->count() . ' historiales procesados');
        $totalIngresos = IngresoEgreso::where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = IngresoEgreso::where('tipo', 'egreso')->sum('monto');
        $balance = $totalIngresos - $totalEgresos;
        
        $this->info('💰 Balance calculado:');
        $this->info('   Ingresos: Bs ' . number_format($totalIngresos, 2));
        $this->info('   Egresos: Bs ' . number_format($totalEgresos, 2));
        $this->info('   Balance: Bs ' . number_format($balance, 2));
        $this->info('📦 Stock actual de materiales:');
        $materiales = Material::all();
        foreach ($materiales as $m) {
            $this->info("   {$m->nombre}: {$m->stock_actual} {$m->unidad}");
        }
        
        $this->info('✅ ¡Recálculo completado!');
        
        return 0;
    }
}