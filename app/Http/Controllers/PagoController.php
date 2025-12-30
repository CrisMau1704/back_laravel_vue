<?php
namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index(){ return Pago::with(['estudiante','inscripcion'])->get(); }
    public function store(Request $request){
        $request->validate(['estudiante_id'=>'required','inscripcion_id'=>'required','monto'=>'required','fecha'=>'required']);
        return Pago::create($request->all());
    }
    public function show($id){ return Pago::findOrFail($id); }
    public function update(Request $request,$id){
        $pago=Pago::findOrFail($id);
        $pago->update($request->all());
        return $pago;
    }
    public function destroy($id){
        Pago::findOrFail($id)->delete();
        return response()->noContent();
    }
}
