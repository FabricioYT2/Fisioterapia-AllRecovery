<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ✅ Debe ser PUBLIC para sobrescribir la propiedad padre
    public $withinTransaction = false;

    public function up(): void
    {
        // Pacientes
        Schema::table('pacientes', function (Blueprint $table) {
            $table->index('ci');
            $table->index('email');
            $table->index('nombre');
            $table->index('fecha_registro');
        });

        // Citas
        Schema::table('citas', function (Blueprint $table) {
            $table->index('pacientes_id');
            $table->index('fecha_hora');
            $table->index('estado');
            $table->index(['pacientes_id', 'estado']);
            $table->index(['fecha_hora', 'estado']);
        });

        // Historial Clínico
        Schema::table('historial_clinicos', function (Blueprint $table) {
            $table->index('citas_id');
            $table->index('codigo_acceso');
            $table->index('created_at');
        });

        // Materiales
        Schema::table('materiales', function (Blueprint $table) {
            $table->index('nombre');
            $table->index(['stock_actual', 'stock_minimo']);
        });

        // Compras
        Schema::table('compra_materiales', function (Blueprint $table) {
            $table->index('fecha_compra');
        });

        // Detalle Compras
        Schema::table('detalle_compra_materiales', function (Blueprint $table) {
            $table->index('materiales_id');
            $table->index('compra_materiales_id');
        });

        // Ingresos/Egresos
        Schema::table('ingresos_egresos', function (Blueprint $table) {
            $table->index('fecha');
            $table->index('tipo');
            $table->index(['fecha', 'tipo']);
        });

        // Cobros
        Schema::table('cobros', function (Blueprint $table) {
            $table->index('citas_id');
            $table->index('estado');
            $table->index('fecha_emision');
        });

        // Servicios
        Schema::table('servicios', function (Blueprint $table) {
            $table->index('historial_clinicos_id');
        });

        // Historial Materiales
        Schema::table('historial_materiales', function (Blueprint $table) {
            $table->index('historial_clinicos_id');
            $table->index('materiales_id');
        });

        // Ejercicios
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->index('nombre_ejercicio');
        });

        // Pivot Paciente-Ejercicios
        Schema::table('paciente_ejercicios', function (Blueprint $table) {
            $table->index('pacientes_id');
            $table->index('ejercicios_id');
        });

        // Pivot Historial-Ejercicios
        Schema::table('historial_ejercicios', function (Blueprint $table) {
            $table->index('historial_clinicos_id');
            $table->index('ejercicios_id');
        });

        // Índices compuestos avanzados (SIN CONCURRENTLY para compatibilidad)
        DB::statement('CREATE INDEX IF NOT EXISTS idx_citas_paciente_fecha_estado ON citas(pacientes_id, fecha_hora, estado)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_historial_clinicos_cita_created ON historial_clinicos(citas_id, created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_pacientes_nombre_ci ON pacientes(nombre, ci)');
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropIndex(['ci']);
            $table->dropIndex(['email']);
            $table->dropIndex(['nombre']);
            $table->dropIndex(['fecha_registro']);
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex(['pacientes_id']);
            $table->dropIndex(['fecha_hora']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['pacientes_id', 'estado']);
            $table->dropIndex(['fecha_hora', 'estado']);
        });

        Schema::table('historial_clinicos', function (Blueprint $table) {
            $table->dropIndex(['citas_id']);
            $table->dropIndex(['codigo_acceso']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('materiales', function (Blueprint $table) {
            $table->dropIndex(['nombre']);
            $table->dropIndex(['stock_actual', 'stock_minimo']);
        });

        Schema::table('compra_materiales', function (Blueprint $table) {
            $table->dropIndex(['fecha_compra']);
        });

        Schema::table('detalle_compra_materiales', function (Blueprint $table) {
            $table->dropIndex(['materiales_id']);
            $table->dropIndex(['compra_materiales_id']);
        });

        Schema::table('ingresos_egresos', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
            $table->dropIndex(['tipo']);
            $table->dropIndex(['fecha', 'tipo']);
        });

        Schema::table('cobros', function (Blueprint $table) {
            $table->dropIndex(['citas_id']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_emision']);
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->dropIndex(['historial_clinicos_id']);
        });

        Schema::table('historial_materiales', function (Blueprint $table) {
            $table->dropIndex(['historial_clinicos_id']);
            $table->dropIndex(['materiales_id']);
        });

        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropIndex(['nombre_ejercicio']);
        });

        Schema::table('paciente_ejercicios', function (Blueprint $table) {
            $table->dropIndex(['pacientes_id']);
            $table->dropIndex(['ejercicios_id']);
        });

        Schema::table('historial_ejercicios', function (Blueprint $table) {
            $table->dropIndex(['historial_clinicos_id']);
            $table->dropIndex(['ejercicios_id']);
        });

        DB::statement('DROP INDEX IF EXISTS idx_citas_paciente_fecha_estado');
        DB::statement('DROP INDEX IF EXISTS idx_historial_clinicos_cita_created');
        DB::statement('DROP INDEX IF EXISTS idx_pacientes_nombre_ci');
    }
};