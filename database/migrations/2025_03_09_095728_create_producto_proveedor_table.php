<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('producto_proveedor', function (Blueprint $table) {
        $table->unsignedBigInteger('id_producto');
        $table->unsignedBigInteger('id_proveedor');
        $table->decimal('precio_compra', 10, 2)->nullable();
        $table->integer('tiempo_entrega_dias')->nullable();
        
        $table->primary(['id_producto', 'id_proveedor']);

        $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
        $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedor');
    }
};
