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
    Schema::create('compras', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_proveedor');
        $table->dateTime('fecha');
        $table->string('tipo_comprobante')->nullable();
        $table->string('num_comprobante')->nullable();
        $table->decimal('total', 10, 2)->default(0);
        $table->timestamps();

        $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
