<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estudiante; // <- Importar la clase Estudiante

class Sucursal extends Model
{
    use HasFactory;

    // Si tu tabla se llama 'sucursales' en la base de datos
    protected $table = 'sucursales';

    protected $fillable = ['nombre', 'direccion', 'telefono', 'estado'];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }
}
