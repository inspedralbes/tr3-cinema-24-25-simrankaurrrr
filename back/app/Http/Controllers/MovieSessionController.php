<?php
namespace App\Http\Controllers;

use App\Models\MovieSession;
use Illuminate\Http\Request;

class MovieSessionController extends Controller
{
    public function index()
    {
        $sessions = MovieSession::all();
        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'session_time' => 'required|date',
            'normal_price' => 'required|numeric|min:0',
            'vip_price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
            'vip_discount_price' => 'required|numeric|min:0',
        ]);

        $session = MovieSession::create($request->all());

        return response()->json($session, 201);
    }

    public function show($id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }
        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        $request->validate([
            'movie_id' => 'exists:movies,id',
            'session_time' => 'date',
            'normal_price' => 'numeric|min:0',
            'vip_price' => 'numeric|min:0',
            'discount_price' => 'numeric|min:0',
            'vip_discount_price' => 'numeric|min:0',
        ]);

        $session->update($request->only(['movie_id', 'session_time', 'normal_price', 'vip_price', 'discount_price', 'vip_discount_price']));

        return response()->json($session);
    }

    public function destroy($id)
    {
        $session = MovieSession::find($id);
        if (!$session) {
            return response()->json(['message' => 'Session not found'], 404);
        }

        $session->delete();
        return response()->json(['message' => 'Session deleted successfully']);
    }
}
