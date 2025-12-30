<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('inscripciones', function (Blueprint $table) {
        $table->foreignId('modalidad_id')
              ->after('estudiante_id')
              ->constrained('modalidades');
    });
}

public function down()
{
    Schema::table('inscripciones', function (Blueprint $table) {
        $table->dropForeign(['modalidad_id']);
        $table->dropColumn('modalidad_id');
    });
}

};
