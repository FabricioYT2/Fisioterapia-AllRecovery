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
        Schema::create('historial_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historial_clinicos_id')->constrained()->onDelete('cascade');
            $table->foreignId('materials_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad_usada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_materials');
    }
};
