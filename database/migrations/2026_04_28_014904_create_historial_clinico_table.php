<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_clinicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citas_id')->constrained()->onDelete('cascade');
            $table->text('evaluacion')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->text('recetas_compra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_clinicos');
    }
};