<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
     {
         $limit = isset($request->limit) ? $request->limit : 10;
     
         if (isset($request->q)) {
             $clientes = Cliente::where('nombre_completo', "like", "%" . $request->q . "%")
                 ->orderBy("id", "desc")
                 ->paginate($limit);
         } else {
             $clientes = Cliente::orderBy("id", "desc")->paginate($limit);
         }
     
         // Devuelve la respuesta correcta para paginación
         return response()->json($clientes, 200);
     }
     

    public function buscarCliente(Request $request)
    {
        // Verifica si se envió el parámetro 'q'
        if(isset($request->q)){
            $cliente = Cliente::where('nombre_completo', 'like', "%".$request->q."%")->first();
            return response()->json($cliente, 200);
        }
    
        // Retornar una respuesta en caso de que no se envíe 'q'
        return response()->json(['error' => 'Parámetro q no proporcionado'], 400);
    }
    
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre_completo" => "required"
        ]);
        //guardar
        $clie = new Cliente();
        $clie->nombre_completo = $request->nombre_completo;
        $clie->ci_nit = $request->ci_nit;
        $clie->telefono = $request->telefono;
        $clie->save();

        //responder
        return response()->json($clie, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clie = Cliente::findOrFail($id);
        return response()->json($clie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $clie = Cliente::find($id);
        $clie->nombre_completo = $request->nombre_completo;
        $clie->ci_nit = $request->ci_nit;
        $clie->telefono = $request->telefono;
        $clie->update();

        return response()->json(["mensaje" => "cliente actualizado correctamente"], 201);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
