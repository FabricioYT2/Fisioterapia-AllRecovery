<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paciente_ejercicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pacientes_id')->constrained()->deferrable()->onDelete('cascade');
            $table->foreignId('ejercicios_id')->constrained()->onDelete('cascade');   
            $table->date('fecha_asignacion');
            $table->integer('series');
            $table->integer('repeticiones');
            $table->string('frecuencia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paciente_ejercicios');
    }
};
