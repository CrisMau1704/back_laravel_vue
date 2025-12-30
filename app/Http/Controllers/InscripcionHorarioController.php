<?php
namespace App\Http\Controllers;

use App\Models\InscripcionHorario;
use Illuminate\Http\Request;

class InscripcionHorarioController extends Controller
{
    public function index(){ return InscripcionHorario::with(['inscripcion','horario'])->get(); }
    public function store(Request $request){
        $request->validate(['inscripcion_id'=>'required','horario_id'=>'required']);
        return InscripcionHorario::create($request->all());
    }
    public function show($id){ return InscripcionHorario::findOrFail($id); }
    public function update(Request $request,$id){
        $ih=InscripcionHorario::findOrFail($id);
        $ih->update($request->all());
        return $ih;
    }
    public function destroy($id){
        InscripcionHorario::findOrFail($id)->delete();
        return response()->noContent();
    }
}
