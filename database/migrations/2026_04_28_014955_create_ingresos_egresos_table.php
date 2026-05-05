<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos_egresos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->string('descripcion');
            $table->foreignId('cobros_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('compra_materiales_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos_egresos');
    }
};