<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlatoProducto extends Pivot
{
    protected $table = 'plato_producto';

    protected $fillable = [
        'id_plato',
        'id_producto',
        'cantidad_usada',
    ];

    public $timestamps = false; // si no tienes campos created_at y updated_at
}
