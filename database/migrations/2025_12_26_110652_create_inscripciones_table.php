<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('estudiante_id')
                ->constrained('estudiantes')
                ->onDelete('cascade');

            $table->foreignId('sucursal_id')
                ->constrained('sucursales');

            $table->foreignId('entrenador_id')
                ->constrained('entrenadores');

            // Fechas (antigüedad)
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Control de clases
            $table->integer('clases_totales');
            $table->integer('clases_asistidas')->default(0);
            $table->integer('permisos_usados')->default(0);

            // Pago personalizado
            $table->decimal('monto_mensual', 8, 2);

            // Estado del estudiante
            $table->enum('estado', [
                'activa',
                'suspendida',
                'en_mora',
                'vencida',
                'finalizada'
            ])->default('activa');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
