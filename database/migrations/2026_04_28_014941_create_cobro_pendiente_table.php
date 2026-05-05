<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobro_pendientes', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('monto_adeudado', 10, 2);
            $table->date('fecha_pago');
            $table->foreignId('cobros_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobro_pendientes');
    }
};