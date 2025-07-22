<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permiso;

class PermisoController extends Controller
{
    public function index()
    {
        return Permiso::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:permisos,nombre',
            'action' => 'required',
            'subject' => 'required',
            'detalle' => 'nullable|string',
        ]);

        $permiso = Permiso::create($request->all());
        return response()->json($permiso, 201);
    }

    public function show($id)
    {
        return Permiso::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);
        $permiso->update($request->all());

        return response()->json($permiso);
    }

    public function destroy($id)
    {
        $permiso = Permiso::findOrFail($id);
        $permiso->delete();

        return response()->json(null, 204);
    }
}
