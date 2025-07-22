<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoPlato;
use App\Models\Plato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = isset($request->limit) ? $request->limit : 10;

        if (isset($request->q)) {
            $pedidos = Pedido::where('id', 'like', "%" . $request->q . "%")
                ->orderBy("id", "desc")
                ->with(['cliente', 'platos.productos']) // Cargar platos y sus productos relacionados
                ->paginate($limit);
        } else {
            $pedidos = Pedido::orderBy("id", "desc")
                ->with(['cliente', 'platos.productos']) // Cargar platos y sus productos relacionados
                ->paginate($limit);
        }

        return response()->json($pedidos);
    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // Crear un nuevo pedido
        $pedido = new Pedido();
        $pedido->cliente_id = $request->input('cliente_id');
        $pedido->user_id = auth()->id(); // Usuario autenticado
        $pedido->estado = $request->input('estado');
        $pedido->metodo_pago = $request->input('metodo_pago');
        $pedido->monto_total = $request->input('monto_total');
        $pedido->observacion = $request->input('observacion');
        $pedido->fecha_entrega = $request->input('fecha_entrega');
        $pedido->save();

        // Verificar si el pedido tiene platos
        if ($request->has('platos')) {
            // Registrar los platos en la tabla intermedia
            $pedidoPlatoController = new PedidoPlatoController();
            $pedidoPlatoController->store($request, $pedido->id); // Guardar los platos en el pivot

            // Cargar los platos con sus productos relacionados
            $pedido->load('platos.productos');

            // Recorrer los platos y actualizar el stock de los productos
            foreach ($pedido->platos as $plato) {
                $cantidadDePlatos = $plato->pivot->cantidad; // Cantidad de platos en el pedido
                Log::info("Procesando plato ID: {$plato->id}, cantidad de platos: {$cantidadDePlatos}");

                // Recorrer los productos asociados a cada plato
                foreach ($plato->productos as $producto) {
                    $cantidadUsada = $producto->pivot->cantidad_usada; // Cantidad de cada producto por plato
                    $cantidadADescontar = $cantidadUsada * $cantidadDePlatos; // Cantidad total a descontar

                    Log::info("Producto ID: {$producto->id} - Cantidad Usada por Plato: {$cantidadUsada} - Cantidad a Descontar: {$cantidadADescontar}");

                    // Verificar si el stock es suficiente
                    if ($producto->stock >= $cantidadADescontar) {
                        $producto->stock -= $cantidadADescontar; // Descontar el stock
                        $producto->save(); // Guardar el cambio en el stock
                        Log::info("Stock actualizado para Producto ID: {$producto->id}. Nuevo stock: {$producto->stock}");
                    } else {
                        // Log de advertencia si el stock no es suficiente
                        Log::warning("No hay suficiente stock para el producto ID: {$producto->id}. Stock disponible: {$producto->stock}, cantidad solicitada: {$cantidadADescontar}");
                    }
                }
            }
        }

        // Responder con éxito y el pedido registrado
        return response()->json([
            'message' => 'Pedido registrado exitosamente',
            'id' => $pedido->id,
            'pedido' => $pedido
        ], 200);
    }






    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Carga los platos con los campos pivot necesarios
        $pedido = Pedido::with(['platos' => function ($query) {
            $query->withPivot('cantidad', 'precio');
        }])->findOrFail($id);

        return response()->json($pedido);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Actualizar el pedido
        $pedido = Pedido::findOrFail($id);
        $pedido->update($request->all());

        return response()->json(["message" => "Pedido actualizado con éxito", "pedido" => $pedido]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eliminar el pedido
        $pedido = Pedido::findOrFail($id);
        $pedido->delete();

        return response()->json(["message" => "Pedido eliminado"]);
    }
    // Supongamos que tienes un pedido con varios platos

}
