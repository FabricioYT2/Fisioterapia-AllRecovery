<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compra_materiales', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal', 10, 2);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->foreignId('materiales_id')->constrained()->onDelete('cascade');
            $table->foreignId('compra_materiales_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compra_materiales');
    }
};