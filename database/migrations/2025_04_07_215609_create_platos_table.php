<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatosTable extends Migration
{
    public function up()
    {
        Schema::create('platos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');  // Nombre del plato
            $table->decimal('precio_venta', 10, 2);  // Precio de venta del plato
            $table->text('descripcion')->nullable();  // Descripción opcional del plato
            $table->unsignedBigInteger('categoria_id');  // Relación con la categoría del plato
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('platos');
    }
}


