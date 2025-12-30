<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('clases_totales');
            $table->integer('clases_por_semana');
            $table->integer('duracion_dias');
            $table->integer('permisos_maximos')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modalidades');
    }
};
