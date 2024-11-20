<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('auth:sanctum')->group(function(){
//rutas para el CRUD de usuarios

Route::get("/usuario", [UserController::class, "funListar"]); //->middleware(["auth:sanctum"]);
Route::post("/usuario", [UserController::class, "funGuardar"]);
Route::get("/usuario/{id}", [UserController::class, "funMostrar"]);
Route::put("/usuario/{id}", [UserController::class, "funModificar"]); 
Route::delete("/usuario/{id}", [UserController::class, "funEliminar"]);

Route::apiResource("/persona", PersonaController::class);
});



Route::apiResource("/producto", ProductoController::class);



Route::prefix("v1/auth")->group(function(){

    Route::post("login", [AuthController::class, "funLogin"]);
    Route::post("register", [AuthController::class, "funRegistro"]);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get("profile", [AuthController::class, "funPerfil"]);
        Route::post("logout", [AuthController::class, "funSalir"]);

    });
});

//login de cristian hola 
Route::get("/no-autorizado", function(){
    return["message" => "No estas autorizado para ver esta pagina"];
})->name('login');