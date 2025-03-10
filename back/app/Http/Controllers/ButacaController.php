<?php
namespace App\Http\Controllers;

use App\Models\Butaca;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ButacaController extends Controller
{
    public function verificarReserva($session_id, $butaca_id)
{
    // Buscar la reserva en la tabla de reservas por la sesión y la butaca
    $reserva = Reserva::where('movie_session_id', $session_id)
        ->where('butaca_id', $butaca_id)
        ->first();

    // Si la reserva existe, devolver el estado y el ID del usuario
    if ($reserva) {
        return response()->json([
            'estado' => 'reservada',
            'butaca_id' => $butaca_id,
            'session_id' => $session_id,
            'user_id' => $reserva->user_id, // ID del usuario que hizo la reserva
        ], 200);
    } else {
        return response()->json([
            'estado' => 'disponible',
            'butaca_id' => $butaca_id,
            'session_id' => $session_id,
        ], 200);
    }
}

    // Método para obtener una butaca por su ID
    public function show($id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }
        return response()->json($butaca);
    }

    // Método para crear una nueva butaca
    public function store(Request $request)
    {
        $request->validate([
            'movie_session_id' => 'required|exists:movie_sessions,id',
            'fila' => 'required|string|in:A,B,C,D,E,F,G,H,I,J,K,L', // Restricción de las filas A-L
            'columna' => 'required|integer|min:1|max:10', // Restricción de las columnas 1-10
            'vip' => 'boolean',
        ]);

        // Crear la butaca
        $butaca = Butaca::create([
            'movie_session_id' => $request->movie_session_id,
            'fila' => $request->fila,
            'columna' => $request->columna,
            'ocupada' => false,
            'vip' => $request->vip,
        ]);

        return response()->json($butaca, 201);
    }

    // Método para actualizar una butaca
    public function update(Request $request, $id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }

        $request->validate([
            'ocupada' => 'boolean',
            'vip' => 'boolean',
        ]);

        $butaca->update($request->only(['ocupada', 'vip']));
        return response()->json($butaca);
    }

    // Método para eliminar una butaca
    public function destroy($id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }

        $butaca->delete();
        return response()->json(['message' => 'Butaca eliminada correctamente']);
    }
}
