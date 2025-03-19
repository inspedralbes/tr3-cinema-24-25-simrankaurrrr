<?php

namespace App\Http\Controllers;

use App\Models\MovieSession;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Compra;


class MovieSessionController extends Controller
{
    public function manageSession(Request $request, $id = null)
    {
        if ($request->isMethod('get')) {
            if ($id) {
                $session = MovieSession::where('id', $id)
                                       ->whereHas('movie', function ($query) {
                                           $query->where('disponible_en_streaming', 1);
                                       })
                                       ->where('visible_para_usuarios', 1)
                                       ->first();
                return $session ? response()->json($session) : response()->json(['message' => 'Sesión no encontrada o no disponible'], 404);
            }
            return response()->json(MovieSession::whereHas('movie', function ($query) {
                $query->where('disponible_en_streaming', 1);
            })->where('visible_para_usuarios', 1)->get());
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'movie_id' => 'required|exists:movies,id',
                'session_time' => 'required|in:16:00:00,18:00:00,20:00:00',
                'session_date' => 'required|date|after:today',
                'dia_espectador' => 'required|boolean',
                'visible_para_usuarios' => 'required|boolean',
            ]);

            $movie = Movie::find($request->movie_id);
            if (!$movie || $movie->disponible_en_streaming == 0) {
                return response()->json(['message' => 'La película no está disponible para sesiones'], 400);
            }

            $existingSession = MovieSession::where('movie_id', $request->movie_id)
                                           ->whereDate('session_date', $request->session_date)
                                           ->where('session_time', $request->session_time)
                                           ->first();
            if ($existingSession) {
                return response()->json(['message' => 'Ya existe una sesión para este horario'], 400);
            }

            return response()->json(MovieSession::create($request->all()), 201);
        }

        if ($request->isMethod('put')) {
            $session = MovieSession::find($id);
            if (!$session) {
                return response()->json(['message' => 'Sesión no encontrada'], 404);
            }

            $request->validate([
                'movie_id' => 'exists:movies,id',
                'session_time' => 'in:16:00:00,18:00:00,20:00:00',
                'session_date' => 'date',
                'dia_espectador' => 'boolean',
                'visible_para_usuarios' => 'boolean',
            ]);

            $session->update($request->only(['movie_id', 'session_time', 'session_date', 'dia_espectador', 'visible_para_usuarios']));
            return response()->json($session);
        }

        if ($request->isMethod('delete')) {
            Compra::where('movie_session_id', $id)->delete();
            $session = MovieSession::find($id);
            if ($session) {
                $session->delete();
                return response()->json(['message' => 'Sesión eliminada correctamente.']);
            }
            return response()->json(['message' => 'Sesión no encontrada'], 404);
        }

        return response()->json(['message' => 'Método no permitido'], 405);
    }



public function getOcupacionByDate($movie_id, $session_date)
{
    // Asegúrate de que la fecha esté en el formato correcto
    $sessions = MovieSession::where('movie_id', $movie_id)
                            ->whereDate('session_date', $session_date) 
                            ->get();

    if ($sessions->isEmpty()) {
        return response()->json(['message' => 'No hay sesiones disponibles para esta película en esta fecha'], 404);
    }

    // Aquí deberías añadir lógica para obtener la ocupación de las sesiones
    // Suponiendo que tienes una relación de "butacas" en la sesión, por ejemplo:
    $ocupacion = $sessions->map(function($session) {
        return [
            'session_id' => $session->id,
            'ocupacion' => $session->butacas->count() // Ajusta según cómo manejes las butacas y reservas
        ];
    });

    return response()->json($ocupacion);
}

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
    public function store(Request $request, $movie_id)
{
    // Asegúrate de que el parámetro $movie_id esté siendo pasado correctamente
    if (!$movie_id) {
        return response()->json(['error' => 'No se proporcionó el ID de la película'], 400);
    }

    // Buscar la película por el ID
    $movie = Movie::find($movie_id);

    // Verificar si la película existe y si está disponible en streaming
    if (!$movie) {
        return response()->json(['error' => 'Película no encontrada'], 404);
    }

    if ($movie->disponible_en_streaming == 0) {
        return response()->json(['error' => 'La película no está disponible para sesiones en streaming'], 400);
    }

    // Lógica de validación de la sesión
    $validated = $request->validate([
        'session_date' => 'required|date|after:today', // Asegúrate de que la fecha sea válida y posterior a hoy
        'session_time' => 'required|date_format:H:i:s|in:16:00:00,18:00:00,20:00:00', // Asegúrate de que sea uno de los horarios válidos
        'dia_espectador' => 'boolean',
        'visible_para_usuarios' => 'boolean', // Otras validaciones si las necesitas
    ]);

    // Comprobar si ya existe una sesión para la misma película, fecha y hora
    $existingSession = MovieSession::where('movie_id', $movie_id)
                                   ->whereDate('session_date', $validated['session_date'])
                                   ->where('session_time', $validated['session_time'])
                                   ->first();

    if ($existingSession) {
        return response()->json(['message' => 'Ya existe una sesión para este horario'], 400);
    }

    // Crear la nueva sesión
    $session = MovieSession::create([
        'movie_id' => $movie_id,
        'session_date' => $validated['session_date'],
        'session_time' => $validated['session_time'],
        'dia_espectador' => $request->dia_espectador ?? false,
        'visible_para_usuarios' => $request->visible_para_usuarios ?? true, // Valor por defecto si no está proporcionado
    ]);

    // Responder con la sesión creada
    return response()->json($session, 201);
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
        // Primero eliminamos las compras asociadas a esta sesión
        Compra::where('movie_session_id', $id)->delete();
    
        // Luego eliminamos la sesión de película
        $session = MovieSession::findOrFail($id);
        $session->delete();
    
        return response()->json(['message' => 'Sesión eliminada correctamente.']);
    }
    
}
