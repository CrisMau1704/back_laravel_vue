<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get("/usuario", [UserController::class, "funListar"]);
Route::post("/usuario", [UserController::class, "funGuardar"]);
Route::get("/usuario/{id}", [UserController::class, "funMostrar"]);
Route::put("/usuario/{id}", [UserController::class, "funModificar"]);
Route::delete("/usuario/{id}", [UserController::class, "funEliminar"]);


Route::apiresource("/persona", UserController::class);
