<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        return response()->json(Movie::all());
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $movie = Movie::create($request->all());
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
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        $request->validate([
            'title' => 'string|max:255',
            'sinopsis' => 'string',
            'duracion' => 'string',
            'director' => 'string',
            'actores' => 'string',
            'año' => 'integer',
            'genero' => 'string',
            'poster_url' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'idioma' => 'nullable|string',
            'subtitulos' => 'boolean',
            'formato' => 'nullable|string',
            'disponible_en_streaming' => 'boolean',
        ]);

        $movie->update($request->all());
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
        // Validar que el valor de disponible_en_streaming sea 0 o 1
        $request->validate([
            'disponible_en_streaming' => 'required|in:0,1'
        ]);

        // Buscar la película por ID
        $movie = Movie::find($movie_id);

        // Si la película no se encuentra, responder con un error 404
        if (!$movie) {
            return response()->json(['message' => 'Película no encontrada'], 404);
        }

        // Actualizar el campo disponible_en_streaming con el valor proporcionado
        $movie->disponible_en_streaming = $request->input('disponible_en_streaming');
        $movie->save();

        // Devolver respuesta exitosa
        return response()->json(['message' => 'Estado de disponibilidad actualizado', 'movie' => $movie]);
    }
}
