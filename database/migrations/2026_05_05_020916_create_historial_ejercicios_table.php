<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_ejercicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_clinicos_id')->constrained()->onDelete('cascade');
            $table->foreignId('ejercicios_id')->constrained()->onDelete('cascade');
            $table->integer('series')->default(3);
            $table->integer('repeticiones')->default(10);
            $table->string('frecuencia');
            $table->text('notas_adicionales')->nullable();
            $table->date('fecha_asignacion');
            $table->timestamps();
            
            $table->unique(['historial_clinicos_id', 'ejercicios_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_ejercicios');
    }
};