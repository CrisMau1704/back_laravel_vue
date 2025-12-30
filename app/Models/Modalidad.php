<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    use HasFactory;
    // NO usar SoftDeletes aquí

    protected $table = 'modalidades';

    protected $fillable = [
        'disciplina_id',
        'nombre',
        'precio_mensual',
        'descripcion',
        'permisos_maximos',
        'estado'
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'permisos_maximos' => 'integer',
        'estado' => 'string'
    ];

    protected $attributes = [
        'estado' => 'activo',
        'permisos_maximos' => 3,
        'precio_mensual' => 0
    ];

    // Relaciones
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // Accesores
    public function getClasesTotalesAttribute()
    {
        return 12; // Siempre 12 clases
    }

    public function getClasesMensualesAttribute()
    {
        return 12; // Siempre 12 clases por mes
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->precio_mensual, 2);
    }

    public function getDisciplinaNombreAttribute()
    {
        return $this->disciplina ? $this->disciplina->nombre : 'Sin disciplina';
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorDisciplina($query, $disciplina_id)
    {
        return $query->where('disciplina_id', $disciplina_id);
    }
}