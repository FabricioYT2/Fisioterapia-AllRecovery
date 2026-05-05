<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            // 1. Hacemos el historial opcional (para poder crear servicios genéricos/catálogo)
            $table->foreignId('historial_clinicos_id')->nullable()->change();
            
            // 2. Agregamos el campo para las sesiones recomendadas de este servicio
            $table->integer('sesiones_recomendadas')->nullable()->default(1)->after('duracion_minutos');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            // Revertimos el cambio (cuidado si ya tienes datos vinculados)
            $table->foreignId('historial_clinicos_id')->nullable(false)->change();
            $table->dropColumn('sesiones_recomendadas');
        });
    }
};