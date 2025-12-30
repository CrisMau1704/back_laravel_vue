<?php
namespace App\Http\Controllers;

use App\Models\Entrenador;
use Illuminate\Http\Request;

class EntrenadorController extends Controller
{
    public function index(Request $request)
{
    $limit = $request->input('limit', 10);
    $q = $request->input('q', '');

    $query = Entrenador::query();

    if ($q) {
        $query->where('nombres', 'like', "%$q%");
              
    }

    $entrenadores = $query->paginate($limit);

    return response()->json([
        'data' => $entrenadores->items(),
        'total' => $entrenadores->total()
    ]);
}
    public function store(Request $request){
        $request->validate(['nombres'=>'required','apellidos'=>'required','telefono'=>'nullable']);
        return Entrenador::create($request->all());
    }
    public function show($id){ return Entrenador::findOrFail($id); }
    public function update(Request $request,$id){
        $entrenador=Entrenador::findOrFail($id);
        $entrenador->update($request->all());
        return $entrenador;
    }
    public function destroy($id){
        Entrenador::findOrFail($id)->delete();
        return response()->noContent();
    }
}
