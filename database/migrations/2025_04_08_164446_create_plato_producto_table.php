<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('plato_producto', function (Blueprint $table) {
            $table->foreignId('id_plato')->constrained('platos')->onDelete('cascade');
            $table->foreignId('id_producto')->constrained('productos')->onDelete('cascade');
            $table->decimal('cantidad_usada', 8, 2)->default(1);  // Cantidad de producto usada en el plato
            $table->boolean('estado')->default(1);  // 1 = activo, 0 = inactivo
            $table->primary(['id_plato', 'id_producto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plato_producto');
    }
};


