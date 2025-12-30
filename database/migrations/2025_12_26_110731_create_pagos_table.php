<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')
                  ->constrained('inscripciones')
                  ->onDelete('cascade');

            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'qr', 'tarjeta', 'transferencia']);
            $table->date('fecha_pago');
            $table->enum('estado', ['pagado', 'pendiente', 'anulado'])->default('pagado');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};