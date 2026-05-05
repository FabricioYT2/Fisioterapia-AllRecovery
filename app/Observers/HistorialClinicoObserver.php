<?php

namespace App\Observers;

use App\Models\HistorialClinico;
use App\Models\Material;

class HistorialClinicoObserver
{
    public function created(HistorialClinico $historial)
    {
        foreach ($historial->materialesUsados as $materialUsado) {
            $material = Material::find($materialUsado->materiales_id);
            if ($material) {
                $material->stock_actual -= $materialUsado->cantidad_usada;
                $material->save();
                if ($material->stock_actual <= $material->stock_minimo) {
                    \Filament\Notifications\Notification::make()
                        ->title('⚠️ Stock bajo')
                        ->body("El material '{$material->nombre}' tiene stock bajo: {$material->stock_actual} {$material->unidad}")
                        ->warning()
                        ->sendToDatabase(auth()->user());
                }
            }
        }
    }

    public function updated(HistorialClinico $historial)
    {
        $this->revertirStock($historial);
        foreach ($historial->materialesUsados as $materialUsado) {
            $material = Material::find($materialUsado->materiales_id);
            if ($material) {
                $material->stock_actual -= $materialUsado->cantidad_usada;
                $material->save();
            }
        }
    }

    public function deleted(HistorialClinico $historial)
    {
        $this->revertirStock($historial);
    }

    private function revertirStock(HistorialClinico $historial)
    {
        foreach ($historial->materialesUsados as $materialUsado) {
            $material = Material::find($materialUsado->materiales_id);
            if ($material) {
                $material->stock_actual += $materialUsado->cantidad_usada;
                $material->save();
            }
        }
    }
}