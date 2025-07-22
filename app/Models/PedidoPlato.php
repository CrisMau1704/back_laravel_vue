<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoPlato extends Model
{
    use HasFactory;

    // 👇 AÑADÍ esto para corregir el nombre de tabla
    protected $table = 'pedido_plato';

    protected $fillable = [
        'pedido_id',
        'plato_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function plato()
    {
        return $this->belongsTo(Plato::class);
    }
}

