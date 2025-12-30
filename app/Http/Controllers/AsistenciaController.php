<?php
namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(){ return Asistencia::with(['estudiante','inscripcion'])->get(); }
    public function store(Request $request){
        $request->validate(['estudiante_id'=>'required','inscripcion_id'=>'required','fecha'=>'required','estado'=>'required']);
        return Asistencia::create($request->all());
    }
    public function show($id){ return Asistencia::findOrFail($id); }
    public function update(Request $request,$id){
        $asistencia=Asistencia::findOrFail($id);
        $asistencia->update($request->all());
        return $asistencia;
    }
    public function destroy($id){
        Asistencia::findOrFail($id)->delete();
        return response()->noContent();
    }
}
