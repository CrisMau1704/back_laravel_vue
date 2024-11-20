<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function funLogin(Request $request)
    {
        $credenciales = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Cambia la condición de Auth::attempt para que el mensaje de error
        // solo se envíe cuando la autenticación falla.
        if (!Auth::attempt($credenciales)) {
            return response()->json(["message" => "Credenciales incorrectas"], 401);
        }

        $usuario = $request->user();

        $token = $usuario->createToken("Token personal")->plainTextToken;

        return response()->json([
            "access_token" => $token,
            "usuario" => $usuario
        ], 201);
    }

    public function funRegistro(Request $request)
    {
        // Implementa la lógica de registro aquí
    }

    public function funPerfil(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function funSalir(Request $request)
    {
       $request->user()->tokens()->delete();
       return response()->json(["message"=>"salio"],200);
    }
}
