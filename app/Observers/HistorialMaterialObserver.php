<?php

namespace App\Observers;

use App\Models\HistorialMaterial;
use App\Models\Material;

class HistorialMaterialObserver
{
    public function created(HistorialMaterial $registro)
    {
        $material = Material::find($registro->materiales_id);
        if ($material) {
            $material->decrement('stock_actual', $registro->cantidad_usada);
        }
    }

    public function updated(HistorialMaterial $registro)
    {
        if ($registro->isDirty('materiales_id') || $registro->isDirty('cantidad_usada')) {
            $oldMaterial = Material::find($registro->getOriginal('materiales_id'));
            if ($oldMaterial) {
                $oldMaterial->increment('stock_actual', $registro->getOriginal('cantidad_usada'));
            }

            $newMaterial = Material::find($registro->materiales_id);
            if ($newMaterial) {
                $newMaterial->decrement('stock_actual', $registro->cantidad_usada);
            }
        }
    }

    public function deleted(HistorialMaterial $registro)
    {
        $material = Material::find($registro->materiales_id);
        if ($material) {
            $material->increment('stock_actual', $registro->cantidad_usada);
        }
    }
}