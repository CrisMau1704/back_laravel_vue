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
     * Cambiar a BelongsToMany en lugar de BelongsTo.
     */
    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)
                    ->withPivot('cantidad') // Aquí estamos especificando la columna adicional
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

