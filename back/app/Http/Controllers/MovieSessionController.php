<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieSessionController extends Controller
{
    public function getSessionsByMovieAndDate($movie_id, $session_date)
    {
        // Busca las sesiones donde la fecha de la sesión coincide con la fecha proporcionada
        $sessions = MovieSession::where('movie_id', $movie_id)
                                ->whereDate('session_date', $session_date) // Aquí usamos whereDate para comparar solo la fecha
                                ->get();
    
        if ($sessions->isEmpty()) {
            return response()->json(['message' => 'No hay sesiones disponibles para esta película en esta fecha'], 404);
        }
    
        return response()->json($sessions);
    }
    
    public function getSessionsByMovie($movie_id)
    {
        $sessions = MovieSession::where('movie_id', $movie_id)->get();
    
        if ($sessions->isEmpty()) {
            return response()->json(['message' => 'No hay sesiones disponibles para esta película'], 404);
        }
    
        return response()->json($sessions);
    }
    
    public function index()
    {
        $sessions = MovieSession::all();
        return response()->json($sessions);
    }

    public function store(Request $request)
{
    // Validar los datos de entrada
    $request->validate([
        'movie_id' => 'required|exists:movies,id',
        'session_time' => 'required|in:16:00:00,18:00:00,20:00:00', // Validar que la hora sea válida
        'session_date' => 'required|date|after:today', // Validar que la fecha sea futura
        'dia_espectador' => 'required|boolean',
    ], [
        'session_time.in' => 'La hora de la sesión debe ser 16:00:00, 18:00:00 o 20:00:00.', // Mensaje personalizado
        'session_time.required' => 'La hora de la sesión es obligatoria.',
        'session_date.after' => 'La fecha de la sesión debe ser posterior a hoy.',
    ]);
    
    // Verificar si la película está disponible para streaming
    $movie = Movie::find($request->movie_id);
    
    if (!$movie) {
        return response()->json(['message' => 'La película no existe'], 404);
    }

    // Si la columna 'disponible_en_streaming' es 0, no permitir agregar la sesión
    if ($movie->disponible_en_streaming == 0) {
        return response()->json(['message' => 'La película no está disponible para agregar sesiones'], 400);
    }
    
    // Verificar si ya existe una sesión para la misma película en el mismo día y hora
    $existingSession = MovieSession::where('movie_id', $request->movie_id)
                                   ->whereDate('session_date', $request->session_date)
                                   ->where('session_time', $request->session_time)
                                   ->first();
    
    if ($existingSession) {
        return response()->json(['message' => 'Ya existe una sesión para esta película en este horario'], 400);
    }
    
    // Crear la nueva sesión
    try {
        $session = MovieSession::create($request->all());
        return response()->json($session, 201);
    } catch (\Exception $e) {
        // Si ocurre un error inesperado, lo atrapamos y mostramos un mensaje
        \Log::error('Error creating session: ', [
            'error_message' => $e->getMessage(),
            'error_details' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        return response()->json(['message' => 'Error al crear la sesión', 'error' => $e->getMessage()], 500);
    }
}

    

    public function show($id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Sesión no encontrada'], 404);
        }
        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Sesión no encontrada'], 404);
        }

        // Validación de datos
        $request->validate([
            'movie_id' => 'exists:movies,id',
            'session_time' => 'in:16:00:00,18:00:00,20:00:00', // Validar que la hora esté en las opciones válidas
            'session_date' => 'date',
            'dia_espectador' => 'boolean',
        ], [
            'session_time.in' => 'La hora de la sesión debe ser 16:00:00, 18:00:00 o 20:00:00.' // Mensaje personalizado para hora inválida
        ]);

        $session->update($request->only(['movie_id', 'session_time', 'session_date', 'dia_espectador']));

        return response()->json($session);
    }

    public function destroy($id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Sesión no encontrada'], 404);
        }

        $session->delete();
        return response()->json(['message' => 'Sesión eliminada con éxito']);
    }
}
