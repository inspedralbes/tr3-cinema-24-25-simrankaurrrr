<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::all();
        return response()->json($compras);
    }
    public function getComprasPorUsuario($user_id)
    {
        $compras = Compra::where('user_id', $user_id)->get();
        return response()->json($compras);
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'movie_session_id' => 'required|exists:movie_sessions,id',
            'ticket_quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
        ]);
    
        $compra = Compra::create([
            'user_id' => $request->user_id,
            'movie_session_id' => $request->movie_session_id,
            'ticket_quantity' => $request->ticket_quantity,
            'total_amount' => $request->total_amount
        ]);
    
        return response()->json($compra, 201);
    }
    

    public function show($id)
    {
        $compra = Compra::find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra not found'], 404);
        }

        return response()->json($compra);
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra not found'], 404);
        }

        $request->validate([
            'user_id' => 'exists:users,id',
            'movie_session_id' => 'exists:movie_sessions,id',
            'seats' => 'array|min:1',
            'ticket_type' => 'string|in:normal,vip,espectador',
        ]);

        $compra->update($request->all());
        return response()->json($compra);
    }

    public function destroy($id)
    {
        $compra = Compra::find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra not found'], 404);
        }

        $compra->delete();
        return response()->json(['message' => 'Compra deleted successfully']);
    }
}
