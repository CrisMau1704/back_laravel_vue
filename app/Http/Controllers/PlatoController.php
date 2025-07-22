<?php

namespace App\Http\Controllers;

use App\Models\Plato;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PlatoController extends Controller
{
    // Mostrar una lista de platos con paginación
    // En tu PlatoController.php
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $q = $request->input('q', '');

        $platos = Plato::where('nombre', 'like', "%$q%")
            ->with([
                'productos:id,nombre', // Carga solo id y nombre de productos
                'categoria:id,nombre'  // Carga solo id y nombre de categoría
            ])
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($platos, 200);
    }


    // Crear un nuevo plato
    public function store(Request $request)
    {
        // Validación similar a update pero para creación
        $request->validate([
            'nombre' => 'required|unique:platos',
            'precio_venta' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
            'productos' => 'sometimes|array',
            'productos.*' => 'exists:productos,id'
        ]);

        // Crear el plato
        $plato = Plato::create([
            'nombre' => $request->nombre,
            'precio_venta' => $request->precio_venta,
            'categoria_id' => $request->categoria_id,
            'descripcion' => $request->descripcion ?? null, // Campo opcional
        ]);

        // Sincronizar productos si existen en la request
        if ($request->has('productos')) {
            $plato->productos()->sync($request->productos);
        }

        return response()->json([
            'message' => 'Plato creado exitosamente',
            'data' => $plato
        ], 201);
    }





    // Obtener un plato específico por ID
    public function show($id)
    {
        $plato = Plato::with('productos')->find($id); // Cargar productos al obtener un plato específico

        if (!$plato) {
            return response()->json(['message' => 'Plato no encontrado'], 404);
        }

        return response()->json($plato);
    }

    // Actualizar un plato existente
    public function update(Request $request, $id_plato)
    {
        $plato = Plato::find($id_plato);
        if (!$plato) {
            return response()->json(['error' => 'Plato no encontrado'], 404);
        }

        // Aquí actualizas los datos del plato
        $plato->update([
            'nombre' => $request->nombre,
            'precio_venta' => $request->precio_venta,
            'categoria_id' => $request->categoria_id, // Asegúrate de tener esto
        ]);

        // Aquí puedes actualizar la relación de productos, si es necesario
        $plato->productos()->sync($request->productos); // Sincroniza los productos

        return response()->json($plato);
    }




    // Eliminar un plato
    public function destroy($id)
    {
        $plato = Plato::find($id);

        if (!$plato) {
            return response()->json(['message' => 'Plato no encontrado'], 404);
        }

        $plato->delete();

        return response()->json(['message' => 'Plato eliminado']);
    }

    // Buscar platos por nombre u otros filtros
    public function buscarPlato(Request $request)
    {
        $q = $request->input('q', '');
        $platos = Plato::where('nombre', 'like', "%$q%")
            ->with('productos') // Cargar productos al realizar la búsqueda
            ->get();

        return response()->json($platos);
    }

    public function getProductosPorPlato($id)
    {
        $plato = Plato::find($id);
        if ($plato) {
            // Aquí puedes devolver los productos asociados al plato
            return response()->json($plato->productos);
        } else {
            return response()->json(['error' => 'Plato no encontrado'], 404);
        }
    }



public function verificarStock($productoId)
{
    $producto = Producto::find($productoId);

    if ($producto) {
        return response()->json(['tieneStock' => $producto->stock > 0]);
    }

    return response()->json(['tieneStock' => false]);
}

    
public function platosPorCategoria($categoriaId)
{
    $platos = Plato::where('categoria_id', $categoriaId)->get();
    return response()->json($platos);
}

 


    
}
