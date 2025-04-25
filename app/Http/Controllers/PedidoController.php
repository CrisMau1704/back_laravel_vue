<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $limit = isset($request->limit) ? $request->limit : 10;

    // Si existe el parámetro de búsqueda 'q', se realiza la búsqueda
    if (isset($request->q)) {
        $pedidos = Pedido::where('fecha', "like", "%" . $request->q . "%");
        
        // Se ordenan los resultados y se cargan las relaciones necesarias
        $pedidos = $pedidos->orderBy("id", "desc")
            ->with(["cliente", "productos"])
            ->paginate($limit);
    } else {
        // Si no hay parámetro de búsqueda 'q', solo se ordenan los resultados
        $pedidos = Pedido::orderBy("id", "desc")
            ->with(["cliente", "productos"])
            ->paginate($limit);
    }

    return response()->json($pedidos);
}


    /**
     * Store a newly created resource in storage.
     *GUARDAR PEDIDO*/
    public function store(Request $request)
{
    // Validar la entrada
    $request->validate([
        "cliente_id" => "required",
        "productos" => "required|array", // Asegurarse de que los productos sean un array
    ]);

    // Guardar el pedido con estado 1 (en proceso)
    $pedido = new Pedido();
    $pedido->fecha = date("Y-m-d H:i:s");
    $pedido->cliente_id = $request->cliente_id;
    $pedido->estado = 1; // Estado 1 para "en proceso"
    $pedido->observacion = $request->observacion;
    $pedido->user_id = Auth::id();
    $pedido->save();

    // Asignar productos al pedido y actualizar stock
    $productos = $request->productos;  // Asegúrate de que los productos vengan en el formato adecuado
    foreach ($productos as $prod) {
        $producto_id =  $prod["producto_id"];
        $cantidad =  $prod["cantidad"];

        // Verificar que haya suficiente stock
        $producto = Producto::find($producto_id);
        if ($producto && $producto->stock >= $cantidad) {
            // Disminuir el stock del producto
            $producto->stock -= $cantidad;
            $producto->save();

            // Usar attach correctamente para asignar productos al pedido
            $pedido->productos()->attach($producto_id, ['cantidad' => $cantidad]);
        } else {
            // Si no hay suficiente stock, retornar un error
            return response()->json([
                "message" => "No hay suficiente stock para el producto: " . $producto->nombre
            ], 400);  // 400 para Bad Request
        }
    }

    // Actualizar el estado del pedido a completado (opcional, si lo necesitas)
    $pedido->estado = 2; // Estado 2 para "completado"
    $pedido->update();

    return response()->json(["message" => "Pedido registrado con éxito"], 201); //201 significa que se guardó correctamente
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
