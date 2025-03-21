<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado_general', ['por hacer', 'empezando', 'finalizado'])->default('por hacer');
            $table->enum('cumplimiento', ['cumplido', 'no cumplido'])->nullable();
            $table->unsignedBigInteger('area_id');
            $table->timestamps();

            // Clave foránea
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};

