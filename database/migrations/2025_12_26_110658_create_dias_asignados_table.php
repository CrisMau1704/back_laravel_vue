<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dias_asignados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscripcion_id')
                  ->constrained('inscripciones')
                  ->onDelete('cascade');

            $table->enum('dia', [
                'Lunes',
                'Martes',
                'Miercoles',
                'Jueves',
                'Viernes'
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_asignados');
    }
};
