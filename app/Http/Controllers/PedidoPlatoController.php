<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PedidoPlato;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Producto;

class PedidoPlatoController extends Controller
{
    public function store(Request $request, $pedidoId)
    {
        // Validar los datos del request
        $request->validate([
            'platos' => 'required|array',
            'platos.*.plato_id' => 'required|integer|exists:platos,id',
            'platos.*.cantidad' => 'required|integer|min:1',
            'platos.*.precio_venta' => 'required|numeric|min:0',
        ]);

        $pedido = Pedido::findOrFail($pedidoId);

        foreach ($request->platos as $platoData) {
            // Guardar el plato en el pedido
            $pedidoPlato = new PedidoPlato();
            $pedidoPlato->pedido_id = $pedido->id;
            $pedidoPlato->plato_id = $platoData['plato_id'];
            $pedidoPlato->cantidad = $platoData['cantidad'];
            $pedidoPlato->precio_unitario = $platoData['precio_venta'];
            $pedidoPlato->subtotal = $pedidoPlato->cantidad * $pedidoPlato->precio_unitario;
            $pedidoPlato->save();

            // Obtener el plato con sus productos asociados
            $plato = Plato::with('productos')->find($platoData['plato_id']);

            if (!$plato || !method_exists($plato, 'productos')) {
                return response()->json([
                    'message' => 'Plato no encontrado o no tiene relación con productos.'
                ], 404);
            }

            // Ya no descontamos el stock aquí. Lo haremos solo en el PedidoController.
        }

        return response()->json(['message' => 'Platos añadidos al pedido correctamente'], 201);
    }
}
