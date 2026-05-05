<?php

namespace App\Observers;

use App\Models\DetalleCompraMaterial;
use App\Models\Material;

class DetalleCompraMaterialObserver
{
    public function created(DetalleCompraMaterial $detalle)
    {
        $material = Material::find($detalle->materiales_id);
        if ($material) {
            $material->increment('stock_actual', (int) $detalle->cantidad);
        }
    }

    public function updated(DetalleCompraMaterial $detalle)
    {
        if ($detalle->isDirty('cantidad') || $detalle->isDirty('materiales_id')) {
            $oldMaterialId = $detalle->getOriginal('materiales_id');
            $oldCantidad = $detalle->getOriginal('cantidad');
            
            $oldMaterial = Material::find($oldMaterialId);
            if ($oldMaterial) {
                $oldMaterial->decrement('stock_actual', (int) $oldCantidad);
            }
            $newMaterial = Material::find($detalle->materiales_id);
            if ($newMaterial) {
                $newMaterial->increment('stock_actual', (int) $detalle->cantidad);
            }
        }
    }
    public function deleted(DetalleCompraMaterial $detalle)
    {
        $material = Material::find($detalle->materiales_id);
        if ($material) {
            $material->decrement('stock_actual', (int) $detalle->cantidad);
        }
    }
}