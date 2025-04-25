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
    Schema::create('detalle_compra', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_compra');
        $table->unsignedBigInteger('id_producto');
        $table->integer('cantidad');
        $table->decimal('precio_compra', 10, 2);
        $table->decimal('subtotal', 10, 2);
        $table->timestamps();

        $table->foreign('id_compra')->references('id')->on('compras')->onDelete('cascade');
        $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compra');
    }
};
