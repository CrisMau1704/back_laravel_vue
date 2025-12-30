<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    use HasFactory;

    // Indicar el nombre real de la tabla
    protected $table = 'entrenadores';

    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'especialidad',
        'fecha_contrato_inicio',
        'fecha_contrato_fin',
        'estado'
    ];

    public function modalidades()
    {
        return $this->hasMany(Modalidad::class);
    }
    public function disciplinas()
{
    return $this->belongsToMany(
        Disciplina::class,
        'disciplina_entrenador'
    )->withTimestamps();
}
}
