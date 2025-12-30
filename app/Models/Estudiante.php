<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'ci',
        'correo',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'estado',
        'sucursal_id'
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
