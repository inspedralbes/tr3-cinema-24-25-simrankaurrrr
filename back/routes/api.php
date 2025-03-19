<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieSessionController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ButacaController;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagosController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Rutas protegidas con token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('{session_id}/{butaca_id}/liberar', [ButacaController::class, 'liberarButaca']);
    Route::post('/reservar-butaca', [ButacaController::class, 'reservarButaca']);
    Route::post('/comprar-reserva/{reservaId}', [ButacaController::class, 'comprarReserva']);
    // Rutas para el carrito
    Route::post('/realizar-pago', [PagosController::class, 'realizarPago']);
    Route::delete('/reservas/{reserva_id}', [ButacaController::class, 'eliminarReserva']);
    Route::get('/butacas/estado/{session_id}', [ButacaController::class, 'verEstadoSesion']);
    Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getCurrentUser']);
    Route::get('/sesion/resumen', [ButacaController::class, 'obtenerResumenSesion']);
    Route::patch('movies/{movie_id}/update-streaming-status', [MovieController::class, 'updateStreamingStatus']);
    Route::get('/movies/all', [MovieController::class, 'getAllMovies']);  // Nueva ruta para obtener todas las películas
    Route::get('/sessions/movie/{movie_id}', [MovieSessionController::class, 'getSessionsByMovie']);
    Route::post('/sessions/{id}', [MovieSessionController::class, 'store']);

    Route::get('/movies', [MovieController::class, 'index']);


Route::post('/agregar-al-carrito', [ButacaController::class, 'agregarAlCarrito']);
Route::get('/ver-carrito', [ButacaController::class, 'verCarrito']);
Route::post('/confirmar-compra', [ButacaController::class, 'confirmarCompra']);});

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
        Route::get('/usuario/{user_id}', [CompraController::class, 'getComprasPorUsuario']);
    });

    // Rutas para las butacas
    Route::prefix('butacas')->group(function() {
        Route::get('sesion/{session_id}', [ButacaController::class, 'obtenerButacasPorSesion']);
        Route::get('{id}', [ButacaController::class, 'show']);
        Route::put('{id}', [ButacaController::class, 'update']);
        Route::delete('{id}', [ButacaController::class, 'destroy']);

    });
