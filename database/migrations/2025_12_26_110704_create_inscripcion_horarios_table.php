<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscripcion_horarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                  ->constrained('inscripciones')
                  ->onDelete('cascade');

            $table->foreignId('horario_id')
                  ->constrained('horarios');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_horarios');
    }
};