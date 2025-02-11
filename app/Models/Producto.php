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
        return $this->belongsTo(Pedido::class)
                    ->withPivot(['cantidad'])
                    ->withTimestamps();
    }

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'stock',
        'precio',
        'descripcion',
        'estado',
        'categoria_id',
        'imagen',
    ];
}
