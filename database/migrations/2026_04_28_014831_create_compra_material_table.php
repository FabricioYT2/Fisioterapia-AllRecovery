<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra_materiales', function (Blueprint $table) {
            $table->id();
            $table->decimal('costo_total', 10, 2);
            $table->date('fecha_compra');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_materiales');
    }
};