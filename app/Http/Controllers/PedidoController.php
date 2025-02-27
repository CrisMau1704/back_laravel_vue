<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *GUARDAR PEDIDO*/
    public function store(Request $request)
    {
        // Validar la entrada
        $request->validate([
            "cliente_id" => "required",
            "productos" => "required",
        ]);
    
        // Guardar el pedido con estado 1 (en proceso)
        $pedido = new Pedido();
        $pedido->fecha = date("Y-m-d H:i:s");
        $pedido->cliente_id = $request->cliente_id;
        $pedido->estado = 1;
        $pedido->observacion = $request->observacion;
        $pedido->user_id = Auth::id();
        $pedido->save();
    
        // Asignar productos al pedido
        $productos = $request->productos;  // Asegúrate de que los productos vengan en el formato adecuado
        foreach ($productos as $prod) {
            $producto_id =  $prod["producto_id"];
            $cantidad =  $prod["cantidad"];
    
            // Usar attach correctamente
            $pedido->productos()->attach($producto_id, ['cantidad' => $cantidad]);
        }
    
        // Actualizar el estado a completado (opcional, si necesitas cambiar el estado al finalizar el pedido)
        $pedido->estado = 2;
        $pedido->update();
    
        return response()->json(["message" => "Pedido registrado"], 201); //201 significa que se guardó correctamente
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
