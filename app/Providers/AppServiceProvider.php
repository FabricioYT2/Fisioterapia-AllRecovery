<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\HistorialClinico;
use App\Observers\HistorialClinicoObserver;
use App\Models\Cita;
use App\Observers\CitaObserver;
use App\Models\CompraMaterial;
use App\Observers\CompraMaterialObserver;
use App\Models\Cobro;
use App\Observers\CobroObserver;
use App\Models\HistorialMaterial;
use App\Observers\HistorialMaterialObserver;
use App\Models\DetalleCompraMaterial;
use App\Observers\DetalleCompraMaterialObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Cobro::observe(CobroObserver::class);
        Cita::observe(CitaObserver::class);
        HistorialClinico::observe(HistorialClinicoObserver::class);
        CompraMaterial::observe(CompraMaterialObserver::class);
        HistorialMaterial::observe(HistorialMaterialObserver::class);
        DetalleCompraMaterial::observe(DetalleCompraMaterialObserver::class);
        
    }
    
}