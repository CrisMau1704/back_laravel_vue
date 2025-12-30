<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero, crear la tabla disciplinas si no existe
        if (!Schema::hasTable('disciplinas')) {
            Schema::create('disciplinas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
            });
            
            // Insertar disciplinas básicas
            \App\Models\Disciplina::insert([
                ['nombre' => 'MMA', 'descripcion' => 'Artes Marciales Mixtas', 'estado' => 'activo'],
                ['nombre' => 'Karate', 'descripcion' => 'Karate Tradicional', 'estado' => 'activo'],
                ['nombre' => 'Boxeo', 'descripcion' => 'Boxeo Profesional', 'estado' => 'activo'],
                ['nombre' => 'Combo', 'descripcion' => 'Combinación de disciplinas', 'estado' => 'activo'],
                ['nombre' => 'General', 'descripcion' => 'Disciplina General', 'estado' => 'activo'],
            ]);
        }
        
        // Ahora agregar la columna disciplina_id a modalidades
        Schema::table('modalidades', function (Blueprint $table) {
            // Agregar la columna sin foreign key primero para evitar problemas
            $table->unsignedBigInteger('disciplina_id')
                  ->nullable()
                  ->after('id')
                  ->comment('ID de la disciplina relacionada');
        });
        
        // Después agregar la foreign key
        Schema::table('modalidades', function (Blueprint $table) {
            $table->foreign('disciplina_id')
                  ->references('id')
                  ->on('disciplinas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('modalidades', function (Blueprint $table) {
            // Eliminar la foreign key primero
            $table->dropForeign(['disciplina_id']);
            // Eliminar la columna
            $table->dropColumn('disciplina_id');
        });
        
        // Opcional: eliminar tabla disciplinas
        // Schema::dropIfExists('disciplinas');
    }
};