<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = isset($request->limit) ? $request->limit : 10;

        if (isset($request->q)) {
            $productos = Producto::where('nombre', "like", "%" . $request->q . "%")
                                    ->where("estado", true)
                                    ->orderBy("id", "desc")
                                    ->with(["categoria"])
                                    ->paginate($limit);
        } else {
            $productos = Producto::orderBy("id", "desc")->where("estado", true)->with(["categoria"])->paginate($limit);
        }

        return response()->json($productos, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $estado = filter_var($request->estado, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    
        // Procesar y guardar la imagen
        $nombreImagen = null;
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombreImagen = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/productos', $nombreImagen);
        } else {
            // Imagen por defecto si no se proporciona
            $nombreImagen = 'default_image.png';
        }
    
        // Guardar el producto
        $prod = new Producto();
        $prod->nombre = $request->nombre;
        $prod->stock = $request->stock;
        $prod->precio = $request->precio;
        $prod->descripcion = $request->descripcion;
        $prod->estado = $estado;
        $prod->categoria_id = $request->categoria_id;
        $prod->imagen = 'productos/' . $nombreImagen;
        $prod->save();
    
        return response()->json(["message" => "Producto registrado"], 201);
    }
    
    

    public function update(Request $request, $id)
    {
        // Validación de los campos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'stock' => 'required|integer',
            'precio' => 'required|numeric',
            'descripcion' => 'nullable|string',
        ]);

        // Actualización del producto
        $producto = Producto::findOrFail($id);
        $producto->update($validated); // Solo actualiza los datos

        // Retornar el producto actualizado como respuesta
        return response()->json($producto);
    }

    public function updateImage($id, Request $request)
{
    $request->validate([
        'imagen' => 'required|image|mimes:jpg,jpeg,png,bmp,gif,svg|max:2048',  // Valida la imagen
    ]);

    $producto = Producto::findOrFail($id);

    if ($request->hasFile('imagen')) {
        // Eliminar la imagen anterior si existe
        if ($producto->imagen && file_exists(public_path('storage/' . $producto->imagen))) {
            unlink(public_path('storage/' . $producto->imagen));
        }

        // Guardar la nueva imagen
        $imagePath = $request->file('imagen')->store('productos', 'public');
        $producto->imagen = $imagePath;
        $producto->save();

        return response()->json([
            'message' => 'Imagen actualizada correctamente',
        ], 200);
    }

    return response()->json([
        'message' => 'No se ha proporcionado ninguna imagen',
    ], 400);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prod = Producto::findOrFail($id);
        return response()->json($prod);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prod = Producto::findOrFail($id);
        $prod->delete();

        return response()->json(["message" => "Producto eliminado"]);
    }
}
