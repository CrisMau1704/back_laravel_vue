<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = isset($request->limit) ? $request->limit : 10;

        if (isset($request->q)) {
            $proveedores = Proveedor::where('nombre', "like", "%" . $request->q . "%")
                ->where("estado", true)
                ->orderBy("id", "desc")
                ->paginate($limit);
        } else {
            $proveedores = Proveedor::orderBy("id", "desc")->paginate($limit);
        }

        // Devuelve la respuesta correcta para paginación
        return response()->json($proveedores, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            "nombre" => "required" // Verificar que el nombre sea único en la tabla 'proveedores'
        ]);
        // Crear un nuevo proveedor
        $provee = new Proveedor();
        $provee->nombre = $request->nombre;
        $provee->contacto = $request->contacto;
        $provee->telefono = $request->telefono;
        $provee->email = $request->email;
        $provee->direccion = $request->direccion;
        $provee->ci_nit = $request->ci_nit;
     
        
        // Guardar en la base de datos
        $provee->save();

        // Responder con el proveedor creado
        return response()->json($provee, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clie = Proveedor::findOrFail($id);
        return response()->json($clie);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $provee = Proveedor::find($id);
        $provee->nombre = $request->nombre;
        $provee->contacto = $request->contacto;
        $provee->telefono = $request->telefono;
        $provee->email = $request->email;
        $provee->direccion = $request->direccion;
        $provee->ci_nit = $request->ci_nit;
   
        $provee->update();

        return response()->json(["mensaje" => "cliente actualizado correctamente"], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
    
        // En lugar de eliminar físicamente, solo actualizamos el estado a 0
        $proveedor->estado = 0;
        $proveedor->save();
    
        return response()->json(['message' => 'Proveedor eliminado (lógicamente)']);
    }

    public function buscarProveedor(Request $request)
    {
        // Verifica si se envió el parámetro 'q'
        if(isset($request->q)){
            $cliente = Proveedor::where('ci_nit', 'like', "%".$request->q."%")->first();
            return response()->json($cliente, 200);
        }
    
        // Retornar una respuesta en caso de que no se envíe 'q'
        return response()->json(['error' => 'Parámetro q no proporcionado'], 400);
    }
    


   
}
