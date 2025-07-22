<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Importa la clase Str para generar cadenas aleatorias

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
        $prod->precio_compra = $request->precio_compra;
        $prod->unidad_medida = $request->unidad_medida;
        $prod->estado = $estado;
        $prod->categoria_id = $request->categoria_id;
        $prod->imagen = 'productos/' . $nombreImagen;
        $prod->save();

        return response()->json(["message" => "Producto registrado"], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Obtener los datos del request
        $data = $request->all();

        // Actualización del producto
        $producto = Producto::findOrFail($id);
        $producto->update($data); // Actualiza los datos sin validación adicional

        // Retornar el producto actualizado como respuesta
        return response()->json($producto);
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
    public function destroy($id)
    {
        $prod = Producto::findOrFail($id);

        // En lugar de eliminar físicamente, solo actualizamos el estado a 0
        $prod->estado = 0;
        $prod->save();

        return response()->json(['message' => 'Producto eliminado (lógicamente)']);
    }

    /**
     * Actualizar el stock de un producto.
     */
    /**
     * Actualizar el stock de un producto.
     */
    // ProductoController.php

 



}
