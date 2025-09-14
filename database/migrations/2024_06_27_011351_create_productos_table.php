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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 200);
            $table->integer("stock")->default(0);
            $table->decimal("precio_compra", 13, 2);
            $table->text("unidad_medida")->nullable();
            $table->string("imagen")->nullable(); // Puedes agregar default(null) si prefieres ser explícito
            $table->boolean("estado")->default(true);
            $table->unsignedBigInteger("categoria_id"); // Mejor usar unsignedBigInteger
            $table->foreign("categoria_id")->references("id")->on("categorias")->onDelete('cascade'); // Asegura que se eliminen productos si la categoría se elimina
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
