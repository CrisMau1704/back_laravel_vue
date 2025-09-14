<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatoProducto extends Model
{
    use HasFactory;

    protected $table = 'plato_producto'; // Especificar nombre de tabla
    
    protected $fillable = ['id_plato', 'id_producto', 'cantidad_usada'];

    // Relación con Plato
    public function plato()
    {
        return $this->belongsTo(Plato::class, 'id_plato');
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    
    // Eliminar el método productos() duplicado
}