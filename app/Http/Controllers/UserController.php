<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //index
    public function funListar(){
        //return User:: get();

        $usuarios = User::get();
        return response()->json($usuarios, 200);
        
    }

    //store
    public function funGuardar(Request $request){
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->save();

        return response()->json(["mensaje" => "Usuario registrado correctamente"], 201);
    }

    //show
    public function funMostrar($id){

        $user = User::find($id);
        return response()->json($user, 200);
    

        
    }

    //update
    public function funModificar(Request $request, $id){
        $usuario = User::find($id);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->update();

        return response()->json(["mensaje" => "Usuario actualizado correctamente"], 201);
    
    }

    //destroy
    public function funEliminar($id){
        $usuario = User::find($id);
        $usuario->delete();
        return response()->json(["mensaje" => "Usuario eliminado correctamente"], 200);
    
    }
}
