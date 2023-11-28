<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{   
    //index
    public function funListar(){
        $usuario = User::get();
        return response()->json($usuario, 200);

    }

    // store
    public function funGuardar(Request $request){
        $usuario = new User();
        $usuario-> name = $request->name;
        $usuario-> email = $request->email;
        $usuario-> password = bcrypt($request->password);
        $usuario->save();
    
        return response()->json(["mensaje" => "Usuario regitrado correctamente"], 200);
    }


    
    //show
    public function funMostrar($id){
        $user = User::find($id);
        return response()->json($user, 200);
        
    }

    
    public function funModificar(Request $request, $id){
        $usuario = User::find($id);
        $usuario-> name = $request->name;
        $usuario-> email = $request->email;
        $usuario-> password = bcrypt($request->password);
        $usuario->update();
        return response()->json(["mensaje" => "Actualizado correctamente"], 201);
   
    }

    
    public function funEliminar($id){
        $usuario = User::find($id);
        $usuario->delete();
        return response()->json(["mensaje" => "usuario eliminada"], 200);
   
    }
}
