<?php
namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
{
    $limit = $request->input('limit', 10);
    $q = $request->input('q', '');

    $query = Sucursal::query();

    if ($q) {
        $query->where('nombre', 'like', "%$q%");
              
    }

    $sucursales = $query->paginate($limit);

    return response()->json([
        'data' => $sucursales->items(),
        'total' => $sucursales->total()
    ]);
}
    public function store(Request $request){
        $request->validate(['nombre'=>'required|string|max:255','direccion'=>'nullable|string']);
        return Sucursal::create($request->all());
    }
    public function show($id){ return Sucursal::findOrFail($id); }
    public function update(Request $request,$id){
        $sucursal=Sucursal::findOrFail($id);
        $sucursal->update($request->all());
        return $sucursal;
    }
    public function destroy($id){
        Sucursal::findOrFail($id)->delete();
        return response()->noContent();
    }
}
