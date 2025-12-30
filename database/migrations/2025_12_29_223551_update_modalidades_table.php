<?php
// database/migrations/xxxxxx_update_modalidades_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Eliminar columnas innecesarias
        Schema::table('modalidades', function (Blueprint $table) {
            // Eliminar columnas que ya no necesitamos
            $table->dropColumn(['clases_totales', 'clases_por_semana', 'duracion_dias']);
        });
        
        // 2. Agregar nuevas columnas necesarias
        Schema::table('modalidades', function (Blueprint $table) {
            $table->decimal('precio_mensual', 8, 2)->default(0)->after('nombre');
            $table->text('descripcion')->nullable()->after('precio_mensual');
            
            // Cambiar permisos_maximos a valor por defecto 3 (ya que todos tienen 3)
            $table->integer('permisos_maximos')->default(3)->change();
        });
        
        // 3. Actualizar datos existentes
        $this->actualizarDatosExistentes();
    }
    
    private function actualizarDatosExistentes(): void
    {
        // Actualizar Plan A
        DB::table('modalidades')->where('id', 1)->update([
            'nombre' => 'MMA Mensual',
            'precio_mensual' => 50.00,
            'descripcion' => '12 clases mensuales de MMA - 3 permisos justificados',
            'permisos_maximos' => 3,
            'updated_at' => now()
        ]);
        
        // Actualizar Plan B
        DB::table('modalidades')->where('id', 2)->update([
            'nombre' => 'Karate Mensual',
            'precio_mensual' => 60.00,
            'descripcion' => '12 clases mensuales de Karate - 3 permisos justificados',
            'permisos_maximos' => 3,
            'updated_at' => now()
        ]);
        
        // Agregar nuevas modalidades si quieres
        DB::table('modalidades')->insertOrIgnore([
            [
                'nombre' => 'Combo Artes Marciales',
                'precio_mensual' => 80.00,
                'descripcion' => '12 clases mensuales (MMA + Karate) - 3 permisos',
                'permisos_maximos' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Boxeo Intensivo',
                'precio_mensual' => 55.00,
                'descripcion' => '12 clases mensuales de Boxeo - 3 permisos',
                'permisos_maximos' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        // Revertir cambios
        Schema::table('modalidades', function (Blueprint $table) {
            // Agregar columnas eliminadas
            $table->integer('clases_totales')->default(12)->after('nombre');
            $table->integer('clases_por_semana')->default(3)->after('clases_totales');
            $table->integer('duracion_dias')->default(30)->after('clases_por_semana');
            
            // Eliminar columnas agregadas
            $table->dropColumn(['precio_mensual', 'descripcion']);
            
            // Restaurar permisos_maximos
            $table->integer('permisos_maximos')->default(3)->change();
        });
        
        // Restaurar datos originales
        DB::table('modalidades')->where('id', 1)->update([
            'nombre' => 'Plan A',
            'clases_totales' => 12,
            'clases_por_semana' => 3,
            'duracion_dias' => 30,
            'permisos_maximos' => 3
        ]);
        
        DB::table('modalidades')->where('id', 2)->update([
            'nombre' => 'Plan B',
            'clases_totales' => 12,
            'clases_por_semana' => 2,
            'duracion_dias' => 45,
            'permisos_maximos' => 3
        ]);
        
        // Eliminar modalidades agregadas
        DB::table('modalidades')->whereIn('nombre', [
            'Combo Artes Marciales',
            'Boxeo Intensivo'
        ])->delete();
    }
};