<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function index(Request $request)
{
    $limit = $request->input('limit', 10);
    $q = $request->input('q', '');

    $query = Estudiante::query();

    if ($q) {
        $query->where('nombres', 'like', "%$q%")
              ->orWhere('apellidos', 'like', "%$q%");
    }

    $estudiantes = $query->paginate($limit);

    return response()->json([
        'data' => $estudiantes->items(),
        'total' => $estudiantes->total()
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci' => 'required|string|unique:estudiantes',
            'correo' => 'nullable|email|unique:estudiantes',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
            'sucursal_id' => 'nullable|exists:sucursales,id'
        ]);

        $estudiante = Estudiante::create($request->all());
        return response()->json($estudiante, 201);
    }

    public function show($id)
    {
        return Estudiante::with('sucursal')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $estudiante = Estudiante::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci' => 'required|string|unique:estudiantes,ci,' . $id,
            'correo' => 'nullable|email|unique:estudiantes,correo,' . $id,
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
            'sucursal_id' => 'nullable|exists:sucursales,id'
        ]);

        $estudiante->update($request->all());
        return response()->json($estudiante);
    }

public function destroy($id)
{
    $estudiante = Estudiante::find($id);

    if (!$estudiante) {
        return response()->json(['message' => 'Estudiante no encontrado'], 404);
    }

    $estudiante->delete();

    return response()->json(['message' => 'Estudiante eliminado']);
}


}
