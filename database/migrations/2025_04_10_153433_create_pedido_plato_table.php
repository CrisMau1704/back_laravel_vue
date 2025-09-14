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
        Schema::create('pedido_plato', function (Blueprint $table) {
            $table->id(); // ID único para la tabla intermedia
            $table->integer('cantidad')->default(1); // Cantidad del plato en el pedido
            $table->decimal('precio_unitario', 10, 2)->default(0); // Precio unitario del plato
            $table->decimal('subtotal', 10, 2)->default(0); // Subtotal calculado (cantidad * precio_unitario)
            $table->text('observaciones')->nullable(); // Observaciones opcionales sobre el plato

            // Relación con la tabla 'pedidos'
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');

            // Relación con la tabla 'platos'
            $table->unsignedBigInteger('plato_id');
            $table->foreign('plato_id')->references('id')->on('platos')->onDelete('cascade');

            $table->timestamps(); // Para las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_plato');
    }
};
