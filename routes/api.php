<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas protegidas por middleware de autenticación
Route::middleware('auth:sanctum')->group(function() {

    // Rutas para el CRUD de usuarios
    Route::get('/usuario', [UserController::class, 'index']);
 
    Route::post("/usuario", [UserController::class, "funGuardar"]);
    Route::get("/usuario/{id}", [UserController::class, "funMostrar"]);
    Route::put("/usuario/{id}", [UserController::class, "funModificar"]); 
    Route::delete("/usuario/{id}", [UserController::class, "funEliminar"]);

    Route::apiResource("/persona", PersonaController::class);
    Route::apiResource('/categoria', CategoriaController::class);
    
    // CRUD de productos con el endpoint adicional para subir imágenes
    Route::apiResource('/producto', ProductoController::class);
  
    // routes/web.php
    Route::post('/productos/{id}/imagen', [ProductoController::class, 'updateImage'])->name('productos.updateImage');

});

Route::prefix("v1/auth")->group(function(){

    Route::post("login", [AuthController::class, "funLogin"]);
    Route::post("register", [AuthController::class, "funRegistro"]);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get("profile", [AuthController::class, "funPerfil"]);
        Route::post("logout", [AuthController::class, "funSalir"]);
        
    });
});

// Ruta para manejo de acceso no autorizado
Route::get("/no-autorizado", function(){
    return response()->json(["message" => "No estás autorizado para ver esta página"], 403);
})->name('login');

