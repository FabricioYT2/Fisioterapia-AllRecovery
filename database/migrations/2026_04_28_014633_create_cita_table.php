<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pacientes_id')->constrained()->onDelete('cascade');
            $table->dateTime('fecha_hora');
            $table->string('estado')->default('pendiente');
            $table->string('motivo');
            $table->string('turno')->nullable();
            $table->string('fuente')->default('admin');
            $table->integer('sesiones_totales')->nullable()->default(1);
            $table->integer('sesion_actual')->nullable()->default(1);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};