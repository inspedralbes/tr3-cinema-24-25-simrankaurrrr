<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieSessionController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ButacaController;

// Rutas para los usuarios
Route::prefix('users')->group(function() {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});

// Rutas para las películas
Route::prefix('movies')->group(function() {
    Route::get('/', [MovieController::class, 'index']);
    Route::post('/', [MovieController::class, 'store']);
    Route::get('{id}', [MovieController::class, 'show']);
    Route::put('{id}', [MovieController::class, 'update']);
    Route::delete('{id}', [MovieController::class, 'destroy']);
});

// Rutas para las sesiones de cine
Route::prefix('sessions')->group(function() {
    Route::get('/', [MovieSessionController::class, 'index']);
    Route::post('/', [MovieSessionController::class, 'store']);
    Route::get('{id}', [MovieSessionController::class, 'show']);
    Route::put('{id}', [MovieSessionController::class, 'update']);
    Route::delete('{id}', [MovieSessionController::class, 'destroy']);
});

// Rutas para las compras de entradas
Route::prefix('compras')->group(function() {
    Route::get('/', [CompraController::class, 'index']);
    Route::post('/', [CompraController::class, 'store']);
    Route::get('{id}', [CompraController::class, 'show']);
    Route::put('{id}', [CompraController::class, 'update']);
    Route::delete('{id}', [CompraController::class, 'destroy']);
    
    // Nueva ruta para que un usuario consulte sus entradas compradas
    Route::get('/usuario/{user_id}', [CompraController::class, 'getComprasPorUsuario']);
});

// Rutas para las butacas
Route::prefix('butacas')->group(function() {
    Route::get('/session/{session_id}', [ButacaController::class, 'getButacasPorSesion']); // Obtener butacas de una sesión específica
    Route::get('{id}', [ButacaController::class, 'show']);
    Route::put('{id}', [ButacaController::class, 'update']);
    Route::delete('{id}', [ButacaController::class, 'destroy']);
});
