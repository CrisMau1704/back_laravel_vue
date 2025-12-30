<?php
namespace App\Http\Controllers;

use App\Models\Modalidad;
use Illuminate\Http\Request;

class ModalidadController extends Controller
{
    public function index() 
    { 
        return Modalidad::with('disciplina')->get(); 
    }
    
    public function store(Request $request) 
    {
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'nombre' => 'required|string|max:255',
            'precio_mensual' => 'required|numeric|min:0',
            'permisos_maximos' => 'required|integer|min:0|max:10',
            'estado' => 'required|in:activo,inactivo'
        ]);
        
        return Modalidad::create($request->all());
    }
    
    public function show($id) 
    { 
        return Modalidad::with('disciplina')->findOrFail($id); 
    }
    
    public function update(Request $request, $id)
    {
        $modalidad = Modalidad::findOrFail($id);
        
        $request->validate([
            'disciplina_id' => 'required|exists:disciplinas,id',
            'nombre' => 'required|string|max:255',
            'precio_mensual' => 'required|numeric|min:0',
            'permisos_maximos' => 'required|integer|min:0|max:10',
            'estado' => 'required|in:activo,inactivo'
        ]);
        
        $modalidad->update($request->all());
        return $modalidad;
    }
    
    public function destroy($id)
    {
        Modalidad::findOrFail($id)->delete();
        return response()->noContent();
    }
}