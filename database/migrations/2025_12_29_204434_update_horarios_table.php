<?php
// database/migrations/2024_01_15_xxxxxx_update_horarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Agregar nuevas columnas a la tabla existente
        Schema::table('horarios', function (Blueprint $table) {
            // 1. Agregar día de la semana
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])
                  ->nullable()
                  ->after('nombre');
            
            // 2. Agregar duración calculada
            $table->integer('duracion_minutos')->default(60)->after('hora_fin');
            
            // 3. Agregar relaciones
            $table->foreignId('disciplina_id')
                  ->nullable()
                  ->constrained('disciplinas')
                  ->onDelete('set null')
                  ->after('duracion_minutos');
            
            $table->foreignId('sucursal_id')
                  ->nullable()
                  ->constrained('sucursales')
                  ->onDelete('set null')
                  ->after('disciplina_id');
            
            $table->foreignId('entrenador_id')
                  ->nullable()
                  ->constrained('entrenadores')
                  ->onDelete('restrict')
                  ->after('sucursal_id');
            
            $table->foreignId('modalidad_id')
                  ->nullable()
                  ->constrained('modalidades')
                  ->onDelete('set null')
                  ->after('entrenador_id');
            
            // 4. Control de capacidad
            $table->integer('cupo_maximo')->default(15)->after('modalidad_id');
            $table->integer('cupo_actual')->default(0)->after('cupo_maximo');
            
            // 5. Estado y configuración
            $table->enum('estado', ['activo', 'inactivo', 'completo'])
                  ->default('activo')
                  ->after('cupo_actual');
            
            $table->string('color', 7)->default('#3B82F6')->after('estado');
            $table->text('descripcion')->nullable()->after('color');
            
            // 6. Soft deletes
            $table->softDeletes();
        });
        
        // 7. Actualizar datos existentes (si los hay)
        $this->actualizarDatosExistentes();
        
        // 8. Hacer que dia_semana sea obligatorio para nuevos registros
        Schema::table('horarios', function (Blueprint $table) {
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])
                  ->nullable(false)
                  ->change();
        });
    }
    
    private function actualizarDatosExistentes(): void
    {
        // Solo si hay datos existentes
        if (DB::table('horarios')->exists()) {
            // Actualizar horarios existentes con valores por defecto
            DB::table('horarios')->update([
                'dia_semana' => 'Lunes',
                'duracion_minutos' => DB::raw('TIMESTAMPDIFF(MINUTE, hora_inicio, hora_fin)'),
                'estado' => 'activo',
                'cupo_maximo' => 15,
                'cupo_actual' => 0,
                'color' => '#3B82F6',
                'updated_at' => now()
            ]);
            
            // Obtener IDs por defecto si las tablas relacionadas existen
            $disciplinaDefault = DB::table('disciplinas')->first();
            $sucursalDefault = DB::table('sucursales')->first();
            $entrenadorDefault = DB::table('entrenadores')->first();
            $modalidadDefault = DB::table('modalidades')->first();
            
            if ($disciplinaDefault) {
                DB::table('horarios')->update(['disciplina_id' => $disciplinaDefault->id]);
            }
            
            if ($sucursalDefault) {
                DB::table('horarios')->update(['sucursal_id' => $sucursalDefault->id]);
            }
            
            if ($entrenadorDefault) {
                DB::table('horarios')->update(['entrenador_id' => $entrenadorDefault->id]);
            }
            
            if ($modalidadDefault) {
                DB::table('horarios')->update(['modalidad_id' => $modalidadDefault->id]);
            }
        }
    }

    public function down(): void
    {
        // Revertir cambios
        Schema::table('horarios', function (Blueprint $table) {
            // Eliminar columnas agregadas
            $table->dropForeign(['disciplina_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropForeign(['entrenador_id']);
            $table->dropForeign(['modalidad_id']);
            
            $table->dropColumn([
                'dia_semana',
                'duracion_minutos',
                'disciplina_id',
                'sucursal_id',
                'entrenador_id',
                'modalidad_id',
                'cupo_maximo',
                'cupo_actual',
                'estado',
                'color',
                'descripcion',
                'deleted_at'
            ]);
        });
    }
};