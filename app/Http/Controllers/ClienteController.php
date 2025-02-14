<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($request)
    {
        

    
    }

    public function buscarCliente(Request $request)
    {
        // Verifica si se envió el parámetro 'q'
        if (!$request->has('q')) {
            return response()->json(['error' => "Falta el parámetro 'q'"], 400);
        }
    
        // Busca clientes que coincidan con el nombre
        $clientes = Cliente::where('nombre_completo', 'like', "%" . $request->q . "%")->get();
    
        // Si no hay resultados, devuelve un error 404
        if ($clientes->isEmpty()) {
            return response()->json(['error' => "Cliente no encontrado"], 404);
        }
    
        return response()->json($clientes, 200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
