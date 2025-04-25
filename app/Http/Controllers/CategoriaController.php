<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    
     public function index(Request $request)
     {
         $limit = isset($request->limit) ? $request->limit : 10;
     
         if (isset($request->q)) {
             $categorias = Categoria::where('nombre', "like", "%" . $request->q . "%")
             ->where("estado", true)    
             ->orderBy("id", "desc")
                 ->paginate($limit);
         } else {
             $categorias = Categoria::orderBy("id", "desc")->paginate($limit);
         }
     
         // Devuelve la respuesta correcta para paginación
         return response()->json($categorias, 200);
     }
     
     
     
     
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validar
        $request->validate([
            "nombre" => "required|unique:categorias"
        ]);
        //guardar
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->detalle = $request->detalle;
        $categoria->save();

        //responder
        return response()->json(["message" => "CATEGORIA REGISTRADA"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json($categoria, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "nombre" => "required|unique:categorias,nombre,$id"
        ]);
        //guardar
        $categoria = Categoria::findOrFail($id);
        $categoria->nombre = $request->nombre;
        $categoria->detalle = $request->detalle;
        $categoria->update();

        //responder
        return response()->json(["message" => "Categoria actualizada"], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        // En lugar de eliminar físicamente, solo actualizamos el estado a 0
        $categoria->estado = 0;
        $categoria->save();
    
        return response()->json(['message' => 'Categoria eliminado (lógicamente)']);
    }
}
