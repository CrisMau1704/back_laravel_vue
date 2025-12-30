<?php
namespace App\Http\Controllers;

use App\Models\DiaAsignado;
use Illuminate\Http\Request;

class DiaAsignadoController extends Controller
{
    public function index(){ return DiaAsignado::all(); }
    public function store(Request $request){
        $request->validate(['inscripcion_id'=>'required','dia'=>'required']);
        return DiaAsignado::create($request->all());
    }
    public function show($id){ return DiaAsignado::findOrFail($id); }
    public function update(Request $request,$id){
        $dia=DiaAsignado::findOrFail($id);
        $dia->update($request->all());
        return $dia;
    }
    public function destroy($id){
        DiaAsignado::findOrFail($id)->delete();
        return response()->noContent();
    }
}
