<?php
namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        return Tarea::with('area')->get();  // Este método ya está correcto
    }

    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
             'estado' => 'required|string|max:255',
            'area_id' => 'required|numeric',  // 'area_id' debe ser un número
        ]);

        // Asignar valor por defecto a 'estado' si no se pasa
        $estado = $request->input('estado', 'pendiente');  // Valor por defecto

        // Si pasa la validación, guardar la tarea
        $tarea = new Tarea();
        $tarea->titulo = $request->input('titulo');
        $tarea->descripcion = $request->input('descripcion');
        $tarea->fecha_inicio = $request->input('fecha_inicio');
        $tarea->fecha_fin = $request->input('fecha_fin');
        $tarea->estado = $estado;  // Asignar 'estado'
        $tarea->area_id = (int) $request->input('area_id');  // Convertir 'area_id' a número
        $tarea->save();

        return response()->json($tarea, 201);
    }

    public function show(Tarea $tarea)
    {
        return $tarea->load('area');
    }

    public function update(Request $request, Tarea $tarea)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:por hacer,empezando,finalizado',
            'cumplimiento' => 'nullable|in:cumplido,no cumplido',
            'area_id' => 'required|exists:areas,id',
        ]);

        $tarea->update($validated);

        return response()->json($tarea, 200);
    }

    public function destroy(Tarea $tarea)
    {
        $tarea->delete();

        return response()->json(null, 204);
    }
}
