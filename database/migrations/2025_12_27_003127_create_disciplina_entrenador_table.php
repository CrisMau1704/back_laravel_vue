<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disciplina_entrenador', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entrenador_id')
                  ->constrained('entrenadores')
                  ->cascadeOnDelete();

            $table->foreignId('disciplina_id')
                  ->constrained('disciplinas')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Evita duplicados
            $table->unique(['entrenador_id', 'disciplina_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplina_entrenador');
    }
};
