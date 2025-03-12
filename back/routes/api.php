<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieSessionController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ButacaController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ReservaController;

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
    Route::get('/movie/{movie_id}', [MovieSessionController::class, 'getSessionsByMovie']);
    Route::get('/movie/{movie_id}/date/{session_date}', [MovieSessionController::class, 'getSessionsByMovieAndDate']);

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

// Rutas para las entradas
Route::prefix('entradas')->group(function() {
    Route::get('/', [EntradaController::class, 'index']); // Obtener todos los tipos de entradas
});

// Rutas para las butacas
Route::prefix('butacas')->group(function() {
    
    Route::get('sesion/{session_id}', [ButacaController::class, 'obtenerButacasPorSesion']);

    Route::get('{id}', [ButacaController::class, 'show']);
    Route::put('{id}', [ButacaController::class, 'update']);
    Route::delete('{id}', [ButacaController::class, 'destroy']);
    // Verificar estado de la butaca
    Route::get('{session_id}/{butaca_id}/estado', [ButacaController::class, 'verificarReserva']);
    
    // Obtener las butacas por sesión
    Route::get('sesion/{session_id}', [ButacaController::class, 'obtenerButacasPorSesion']);

    Route::delete('{session_id}/{butaca_id}/liberar', [ButacaController::class, 'liberarButaca']);

    Route::post('/reservar-butaca', [ButacaController::class, 'reservarButaca']);
    Route::put('{session_id}/{butaca_id}/confirmar', [ButacaController::class, 'confirmarButaca']);
    Route::get('{session_id}/{butaca_id}/estado', [ButacaController::class, 'verificarReserva']);
    // Obtener todas las reservas para una sesión de cine


});
