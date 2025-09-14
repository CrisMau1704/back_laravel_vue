<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * Relación con la tabla `categorias`.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con la tabla `pedidos`.
     */
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    /**
     * Relación con la tabla `platos` (muchos a muchos).
     */
    public function platos()
    {
        return $this->belongsToMany(Plato::class, 'plato_producto', 'id_producto', 'id_plato')
                    ->withPivot('cantidad_usada');
    }

    /**
     * Atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'nombre',
        'stock',
        'precio_compra',
        'unidad_medida',
        'estado',
        'categoria_id',
        'imagen',
    ];
}
