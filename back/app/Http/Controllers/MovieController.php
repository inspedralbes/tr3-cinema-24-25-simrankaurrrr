<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return response()->json($movies);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'plot' => 'required|string',
            'director' => 'required|string',
            'year' => 'required|integer',
            'genre' => 'required|string',
            'poster_url' => 'required|string',
        ]);

        $movie = Movie::create($request->all());
        return response()->json($movie, 201);
    }

    public function show($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }
        return response()->json($movie);
    }

    public function update(Request $request, $id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $request->validate([
            'title' => 'string|max:255',
            'plot' => 'string',
            'director' => 'string',
            'year' => 'integer',
            'genre' => 'string',
            'poster_url' => 'nullable|string',
        ]);

        $movie->update($request->all());
        return response()->json($movie);
    }

    public function destroy($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $movie->delete();
        return response()->json(['message' => 'Movie deleted successfully']);
    }
}
