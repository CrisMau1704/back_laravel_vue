<?php
// database/migrations/2024_01_15_xxxxxx_add_control_fields_to_inscripcion_horarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inscripcion_horarios', function (Blueprint $table) {
            // 1. Agregar campos de control de clases
            $table->integer('clases_asistidas')->default(0)->after('horario_id');
            $table->integer('clases_totales')->default(12)->after('clases_asistidas');
            $table->integer('clases_restantes')->default(12)->after('clases_totales');
            $table->integer('permisos_usados')->default(0)->after('clases_restantes');
            
            // 2. Agregar fechas específicas
            $table->date('fecha_inicio')->nullable()->after('permisos_usados');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            
            // 3. Agregar estado
            $table->enum('estado', ['activo', 'pausado', 'finalizado', 'vencido'])
                  ->default('activo')
                  ->after('fecha_fin');
            
            // 4. Índices para optimización
            $table->index('fecha_fin');
            $table->index('estado');
            $table->index('clases_restantes');
        });
        
        // 5. Actualizar datos existentes (si los hay)
        $this->actualizarDatosExistentes();
    }
    
    private function actualizarDatosExistentes(): void
    {
        // Solo si hay datos existentes en inscripcion_horarios
        if (DB::table('inscripcion_horarios')->exists()) {
            // Obtener información de las inscripciones relacionadas
            $inscripciones = DB::table('inscripciones')
                ->select('id', 'clases_totales', 'clases_restantes', 'permisos_usados')
                ->get()
                ->keyBy('id');
            
            // Actualizar cada registro en inscripcion_horarios
            DB::table('inscripcion_horarios')->get()->each(function ($registro) use ($inscripciones) {
                $inscripcion = $inscripciones[$registro->inscripcion_id] ?? null;
                
                if ($inscripcion) {
                    // Si ya tiene clases_totales en la inscripción, dividirlas entre horarios
                    $horariosCount = DB::table('inscripcion_horarios')
                        ->where('inscripcion_id', $registro->inscripcion_id)
                        ->count();
                    
                    // Distribuir clases entre horarios
                    $clasesPorHorario = $horariosCount > 0 
                        ? ceil($inscripcion->clases_totales / $horariosCount)
                        : $inscripcion->clases_totales;
                    
                    DB::table('inscripcion_horarios')
                        ->where('id', $registro->id)
                        ->update([
                            'clases_totales' => $clasesPorHorario,
                            'clases_restantes' => $clasesPorHorario,
                            'permisos_usados' => 0,
                            'fecha_inicio' => now()->startOfMonth(),
                            'fecha_fin' => now()->endOfMonth(),
                            'estado' => 'activo',
                            'updated_at' => now()
                        ]);
                } else {
                    // Valores por defecto
                    DB::table('inscripcion_horarios')
                        ->where('id', $registro->id)
                        ->update([
                            'clases_totales' => 12,
                            'clases_restantes' => 12,
                            'permisos_usados' => 0,
                            'fecha_inicio' => now()->startOfMonth(),
                            'fecha_fin' => now()->endOfMonth(),
                            'estado' => 'activo',
                            'updated_at' => now()
                        ]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('inscripcion_horarios', function (Blueprint $table) {
            // Eliminar columnas agregadas
            $table->dropColumn([
                'clases_asistidas',
                'clases_totales',
                'clases_restantes',
                'permisos_usados',
                'fecha_inicio',
                'fecha_fin',
                'estado'
            ]);
            
            // Eliminar índices
            $table->dropIndex(['fecha_fin']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['clases_restantes']);
        });
    }
};