<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompraMaterial extends Model
{
    protected $table = 'compra_materiales';
    use HasFactory;

    protected $fillable = [
        'costo_total',
        'fecha_compra',
    ];
    protected $casts = [
        'fecha_compra' => 'date',
    ];
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompraMaterial::class, 'compra_materiales_id');
    }
    public function egreso(): HasOne
    {
        return $this->hasOne(IngresoEgreso::class, 'compra_materiales_id');
    }
        public function detalleCompraMateriales(): HasMany
    {
        return $this->hasMany(DetalleCompraMaterial::class, 'compra_materiales_id')
            ->with('material');
    }
}
