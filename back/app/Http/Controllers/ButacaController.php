<?php
namespace App\Http\Controllers;

use App\Models\Butaca;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ButacaController extends Controller
{
    // Método para obtener el estado de las butacas por sesión
    public function obtenerButacasPorSesion($session_id, Request $request)
    {
        // Obtener todas las reservas para la sesión indicada
        $reservas = Reserva::where('movie_session_id', $session_id)->get();

        // Arreglo para almacenar las butacas con su estado
        $resultado = [];

        // Obtener las butacas asociadas con las reservas
        foreach ($reservas as $reserva) {
            // Obtener la butaca asociada a la reserva
            $butaca = Butaca::find($reserva->butaca_id);

            // Si la butaca existe, se marca como ocupada
            if ($butaca) {
                $estado = $reserva->estado; // Usar el estado de la reserva, como 'reservada', 'confirmada', etc.
                $user_id = $reserva->user_id; // Obtener el ID del usuario que hizo la reserva
            } else {
                // Si no existe la butaca, se omite
                continue;
            }

            // Añadir la butaca al arreglo de resultados con su estado y user_id si está ocupada
            $resultado[] = [
                'butaca_id' => $butaca->id,
                'fila' => $butaca->fila,
                'columna' => $butaca->columna,
                'estado' => $estado,
                'user_id' => $user_id,
            ];
        }

        // Asegurarse de que todas las butacas, incluso si no están reservadas, se muestren
        $butacasDisponibles = Butaca::whereNotIn('id', array_column($resultado, 'butaca_id'))->get();
        
        foreach ($butacasDisponibles as $butaca) {
            $estado = 'disponible'; // Gris para disponibles
            $user_id = null;

            // Verificar si la butaca está seleccionada por el usuario
            if ($request->user() && $this->isUserSelecting($butaca->id, $request->user()->id)) {
                $estado = 'seleccionada'; // Verde para seleccionada por el usuario
            }

            // Añadir la butaca con el estado apropiado
            $resultado[] = [
                'butaca_id' => $butaca->id,
                'fila' => $butaca->fila,
                'columna' => $butaca->columna,
                'estado' => $estado,
                'user_id' => $user_id,
            ];
        }

        // Retornar el resultado en formato JSON
        return response()->json($resultado);
    }

    // Método para verificar si el usuario está seleccionando una butaca
    private function isUserSelecting($butaca_id, $user_id)
    {
        // Aquí deberías implementar la lógica para determinar si una butaca está seleccionada por el usuario
        // Esto podría basarse en una sesión de usuario o un modelo que gestione las selecciones
        // Para este ejemplo, lo dejaré simple como una función que retorna false siempre.
        return false;
    }

    // Método para verificar si una butaca está reservada o no
    public function verificarReserva($session_id, $butaca_id)
    {
        // Buscar la reserva en la tabla de reservas por la sesión y la butaca
        $reserva = Reserva::where('movie_session_id', $session_id)
            ->where('butaca_id', $butaca_id)
            ->first();

        // Si la reserva existe, devolver el estado y el ID del usuario
        if ($reserva) {
            return response()->json([
                'estado' => $reserva->estado, // Ahora se obtiene el estado de la reserva (reservada, confirmada, etc.)
                'butaca_id' => $butaca_id,
                'session_id' => $session_id,
                'user_id' => $reserva->user_id, // ID del usuario que hizo la reserva
            ], 200);
        } else {
            return response()->json([
                'estado' => 'disponible', // Si no está reservada, el estado es 'disponible'
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

    // Método para crear o actualizar una reserva con estado
    public function reservarButaca(Request $request, $session_id, $butaca_id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'estado' => 'required|string|in:reservada,confirmada', // Validación para el estado
        ]);

        // Verificar si ya existe una reserva
        $reserva = Reserva::where('movie_session_id', $session_id)
            ->where('butaca_id', $butaca_id)
            ->first();

        if ($reserva) {
            // Si la reserva ya existe, actualizamos el estado
            $reserva->estado = $request->estado;
            $reserva->save();
        } else {
            // Si no existe la reserva, creamos una nueva
            $reserva = Reserva::create([
                'user_id' => $request->user_id,
                'movie_session_id' => $session_id,
                'butaca_id' => $butaca_id,
                'estado' => $request->estado,
            ]);
        }

        return response()->json($reserva, 200);
    }
}
