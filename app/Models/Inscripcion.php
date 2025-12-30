<?php
// app/Models/Inscripcion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ELIMINA esta línea: use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Inscripcion extends Model
{
    use HasFactory; // SOLO HasFactory, NO SoftDeletes
    
    protected $table = 'inscripciones';
    
    protected $fillable = [
        'estudiante_id',
        'modalidad_id',
        'clases_totales',
        'clases_restantes',
        'permisos_usados',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'clases_totales' => 'integer',
        'clases_restantes' => 'integer',
        'permisos_usados' => 'integer'
    ];

    protected $attributes = [
        'estado' => 'activo',
        'clases_totales' => 12,
        'clases_restantes' => 12,
        'permisos_usados' => 0
    ];

    // Relaciones
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class);
    }

    public function horarios(): BelongsToMany
    {
        return $this->belongsToMany(Horario::class, 'inscripcion_horarios')
                    ->using(InscripcionHorario::class)
                    ->withPivot([
                        'id',
                        'clases_asistidas',
                        'clases_totales',
                        'clases_restantes',
                        'permisos_usados',
                        'fecha_inicio',
                        'fecha_fin',
                        'estado'
                    ])
                    ->withTimestamps();
    }

    public function inscripcionHorarios()
    {
        return $this->hasMany(InscripcionHorario::class, 'inscripcion_id');
    }

    // Scopes básicos
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }
}