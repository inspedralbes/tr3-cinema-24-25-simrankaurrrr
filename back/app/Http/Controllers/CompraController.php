<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Reserva;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index()
    {
        return response()->json(Compra::all());
    }

    public function getComprasPorUsuario($user_id)
    {
        return response()->json(Compra::where('user_id', $user_id)->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'movie_session_id' => 'required|exists:movie_sessions,id',
            'butaca_ids' => 'required|array|min:1',
            'butaca_ids.*' => 'exists:butacas,id',
        ]);

        $reservas = Reserva::where('user_id', $request->user_id)
            ->where('movie_session_id', $request->movie_session_id)
            ->whereNull('compra_id')
            ->whereIn('butaca_id', $request->butaca_ids)
            ->get();

        if ($reservas->isEmpty()) {
            return response()->json(['message' => 'No hay reservas pendientes para estas butacas en esta sesión'], 400);
        }

        $comprasExistentes = Compra::where('user_id', $request->user_id)
            ->where('movie_session_id', $request->movie_session_id)
            ->get();

        $entradasCompradas = $comprasExistentes->sum('ticket_quantity');

        if ($entradasCompradas + count($request->butaca_ids) > 10) {
            return response()->json(['message' => 'No puede comprar más de 10 entradas para esta sesión'], 400);
        }

        $total = $reservas->sum('precio');

        $compra = Compra::create([
            'user_id' => $request->user_id,
            'movie_session_id' => $request->movie_session_id,
            'ticket_quantity' => count($request->butaca_ids),
            'total_amount' => $total,
            'estado' => 'en_proceso',
        ]);

        foreach ($reservas as $reserva) {
            $reserva->compra_id = $compra->id;
            $reserva->save();
        }

        return response()->json($compra, 201);
    }

    public function show($id)
    {
        $compra = Compra::find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }
        return response()->json($compra);
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::find($id);
        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
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
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        $compra->delete();
        return response()->json(['message' => 'Compra eliminada con éxito']);
    }
}