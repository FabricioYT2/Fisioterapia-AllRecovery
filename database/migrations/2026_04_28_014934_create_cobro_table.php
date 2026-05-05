<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citas_id')->constrained()->onDelete('cascade');
            $table->decimal('monto_total', 10, 2);
            $table->date('fecha_emision');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            $table->string('metodo_pago')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->text('nota')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};