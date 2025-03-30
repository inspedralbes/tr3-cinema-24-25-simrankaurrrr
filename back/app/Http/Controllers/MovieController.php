<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        // Filtra solo las películas disponibles en streaming
        $movies = Movie::where('disponible_en_streaming', 1)->get();
        
        return response()->json($movies);
    }
    
 public function getAllMovies()
 {
     $movies = Movie::all();

     return response()->json($movies);
 }
    public function store(Request $request)
    {
        // Validar los datos de la solicitud y almacenar los datos validados en una variable
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'duracion' => 'required|string',
            'director' => 'required|string',
            'actores' => 'required|string',
            'año' => 'required|integer',
            'genero' => 'required|string',
            'poster_url' => 'required|string',
            'trailer_url' => 'nullable|string',
            'idioma' => 'nullable|string',
            'subtitulos' => 'boolean',
            'formato' => 'nullable|string',
            'disponible_en_streaming' => 'boolean',
        ]);
    
        $movie = Movie::create($validated);
    
        return response()->json($movie, 201);
    }
    

    public function show($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }
        return response()->json($movie);
    }

    public function update(Request $request, $id)
    {
        // Buscar la película
        $movie = Movie::find($id);
    
        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }
    
        // Validar los datos enviados en la solicitud
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|string',
            'director' => 'nullable|string',
            'actores' => 'nullable|string',
            'año' => 'nullable|integer',
            'genero' => 'nullable|string',
            'poster_url' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'idioma' => 'nullable|string',
            'subtitulos' => 'nullable|boolean',
            'formato' => 'nullable|string',
            'disponible_en_streaming' => 'nullable|boolean',
        ]);
    
        $movie->update($validated);
    
        return response()->json($movie);
    }
    

    public function destroy($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        $movie->delete();
        return response()->json(['message' => 'Película eliminada correctamente']);
    }
    public function updateStreamingStatus($movie_id, Request $request)
    {
        $request->validate([
            'disponible_en_streaming' => 'required|in:0,1'
        ]);

        // Buscar la película por ID
        $movie = Movie::find($movie_id);

        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        // Actualizar el campo disponible_en_streaming con el valor proporcionado
        $movie->disponible_en_streaming = $request->input('disponible_en_streaming');
        $movie->save();

        return response()->json(['message' => 'Estado de disponibilidad actualizado', 'movie' => $movie]);
    }
}
