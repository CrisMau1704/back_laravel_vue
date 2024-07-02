<?php

use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//rutas para el CRUD de usuarios

Route::get("/usuario", [UserController::class, "funListar"]);
Route::post("/usuario", [UserController::class, "funGuardar"]);
Route::get("/usuario/{id}", [UserController::class, "funMostrar"]);
Route::put("/usuario/{id}", [UserController::class, "funModificar"]); 
Route::delete("/usuario/{id}", [UserController::class, "funEliminar"]);

Route::apiResource("/persona", PersonaController::class);

Route::apiResource("/producto", ProductoController::class);