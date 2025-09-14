<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->smallInteger('estado')->default(1); // 1=proceso, 2=completado, 3=pendiente, 4=cancelado
            $table->text('observacion')->nullable();  // Observaciones opcionales
            $table->decimal('monto_total', 10, 2)->default(0.00);  // Monto total del pedido (calculado)
            $table->dateTime('fecha_entrega')->nullable(); // Fecha estimada de entrega (opcional)

            // Relación con el cliente
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');

            // Relación con el usuario que realiza el pedido
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); // Para las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
