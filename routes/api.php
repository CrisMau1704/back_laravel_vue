<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\PlatoController;
use App\Http\Controllers\TareaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\PedidoPlatoController;


// Rutas protegidas por middleware de autenticación
Route::middleware('auth:sanctum')->group(function () {

    //buscar cliente 
    Route::get('/cliente/buscar-cliente', [ClienteController::class, "buscarCliente"]);
    Route::get('/proveedor/buscar-proveedor', [ProveedorController::class, "buscarProveedor"]);



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

    Route::apiResource("/pedido", PedidoController::class);
    Route::apiResource('/cliente', ClienteController::class);
    Route::apiResource('/proveedor', ProveedorController::class);

    Route::apiResource('/area', AreaController::class);


    Route::apiResource('tarea', TareaController::class);

    Route::get('/users-with-roles', [UserRoleController::class, 'index']);
    Route::get('/roles', [UserRoleController::class, 'getRoles']);
    Route::post('/assign-roles', [UserRoleController::class, 'assignRoles']);

    Route::apiResource('/plato', PlatoController::class);

    Route::post('/productos/{id}/descontar', [ProductoController::class, 'descontarStock']);
    Route::get('platos/{id}/productos', [PlatoController::class, 'getProductosPorPlato']);

    
    Route::post('/pedido/{pedidoId}/platos', [PedidoPlatoController::class, 'store']);

    // routes/api.php
    Route::get('plato/categoria/{categoriaId}', [PlatoController::class, 'platosPorCategoria']);








});

Route::prefix("v1/auth")->group(function () {

    Route::post("login", [AuthController::class, "funLogin"]);
    Route::post("register", [AuthController::class, "funRegistro"]);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get("profile", [AuthController::class, "funPerfil"]);
        Route::post("logout", [AuthController::class, "funSalir"]);
    });
});

// Ruta para manejo de acceso no autorizado
Route::get("/no-autorizado", function () {
    return response()->json(["message" => "No estás autorizado para ver esta página"], 403);
})->name('login');
