<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación muchos a muchos con la tabla `productos` usando la tabla pivot.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class)
                    ->withPivot('cantidad') // Especifica la columna adicional
                    ->withTimestamps();
    }
}
