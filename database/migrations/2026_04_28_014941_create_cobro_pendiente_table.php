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
        Schema::create('cobro_pendientes', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_pagado');
            $table->decimal('monto_adeudado');
            $table->date('fecha_pago');
            $table->foreignId('cobros_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobro_pendientes');
    }
};
