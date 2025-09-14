<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    use HasFactory;

    protected $table = 'platos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'precio_venta',
        'categoria_id', // ✅ Añadido
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'plato_producto', 'id_plato', 'id_producto')
                    ->withPivot('cantidad_usada')
                    ->using(PlatoProducto::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_plato')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'observaciones')
                    ->withTimestamps();
    }
    
    
}
