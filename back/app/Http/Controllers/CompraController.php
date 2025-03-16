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
        // Validación de los datos de la solicitud
        $request->validate([
            'user_id' => 'required|exists:users,id',  // Validar que el user_id sea válido
            'movie_session_id' => 'required|exists:movie_sessions,id',  // Validar que el movie_session_id sea válido
            'butaca_id' => 'required|exists:butacas,id',  // Validar que butaca_id sea válido
        ]);
        
        // Obtener las reservas asociadas al usuario y la sesión de película
        $reservas = Reserva::where('user_id', $request->user_id)
            ->where('movie_session_id', $request->movie_session_id)
            ->whereNull('compra_id')  // Reservas que aún no tienen compra asociada
            ->get();
    
        if ($reservas->isEmpty()) {
            return response()->json(['message' => 'No hay reservas pendientes para esta sesión'], 400);
        }
    
        // Calcular el total de la compra sumando el precio de cada reserva
        $total = $reservas->sum('precio');
        
        // Crear la compra, asegurándonos de que se pase el butaca_id
        $compra = Compra::create([
            'user_id' => $request->user_id,
            'movie_session_id' => $request->movie_session_id,
            'butaca_id' => $request->butaca_id,  // Usar el butaca_id de la solicitud
            'ticket_quantity' => $reservas->count(),
            'total_amount' => $total,
            'estado' => 'en_proceso',  // Estado de la compra inicial
        ]);
    
        // Asociar las reservas a esta compra
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
