<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Definir los campos que permiten asignación masiva
    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'estado',
        'metodo_pago',
        'monto_total', 
        'observacion',
        'fecha_entrega',
    ];
    

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación muchos a muchos con la tabla `productos` usando la tabla pivot.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'pedido_producto', 'pedido_id', 'producto_id')
                    ->withPivot('cantidad');
    }

    // Relación con la tabla pedido_plato
    public function platos()
    {
        return $this->belongsToMany(Plato::class, 'pedido_plato')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal', 'observaciones')
                    ->withTimestamps();
    }
}
