<?php
namespace App\Http\Controllers;

use App\Models\Butaca;
use App\Models\Reserva;
use App\Models\MovieSession;

use Illuminate\Http\Request;

class ButacaController extends Controller
{
 
    public function obtenerButacasPorSesion($session_id, Request $request)
    {
        $reservas = Reserva::where('movie_session_id', $session_id)->get();
        $resultado = [];

        // Obtener la sesión para verificar si es "Día del Espectador"
        $sesion = MovieSession::find($session_id);
        $esDiaEspectador = $sesion->dia_espectador; // Obtenemos el valor de dia_espectador

        // Determinar el ajuste del precio
        $descuento = $esDiaEspectador == 1 ? 2 : 0; // Si es día de espectador, descuento de 2 euros

        foreach ($reservas as $reserva) {
            $butaca = Butaca::find($reserva->butaca_id);

            if ($butaca) {
                $estado = $reserva->estado;
                $user_id = $reserva->user_id;

                // Obtener el precio de la butaca y aplicar el descuento si corresponde
                $precioOriginal = $butaca->precio;
                $precioConDescuento = $precioOriginal - $descuento; // Descontamos 2 euros si es "Día del Espectador"

                $resultado[] = [
                    'butaca_id' => $butaca->id,
                    'fila' => $butaca->fila,
                    'columna' => $butaca->columna,
                    'estado' => $estado,
                    'user_id' => $user_id,
                    'vip' => $butaca->vip,
                    'precio' => $precioConDescuento, // Mostramos el precio con el descuento
                ];
            }
        }

        // Añadir las butacas disponibles
        $butacasDisponibles = Butaca::whereNotIn('id', array_column($resultado, 'butaca_id'))->get();

        foreach ($butacasDisponibles as $butaca) {
            $estado = 'disponible';
            $user_id = null;

            if ($request->user() && $this->isUserSelecting($butaca->id, $request->user()->id)) {
                $estado = 'seleccionada';
            }

            // Obtener el precio de la butaca y aplicar el descuento si corresponde
            $precioOriginal = $butaca->precio;
            $precioConDescuento = $precioOriginal - $descuento; // Descontamos 2 euros si es "Día del Espectador"

            $resultado[] = [
                'butaca_id' => $butaca->id,
                'fila' => $butaca->fila,
                'columna' => $butaca->columna,
                'estado' => $estado,
                'user_id' => $user_id,
                'vip' => $butaca->vip,
                'precio' => $precioConDescuento, // Mostramos el precio con el descuento
            ];
        }

        return response()->json($resultado);
    }

    public function reservarButaca(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'movie_session_id' => 'required|exists:movie_sessions,id',
            'butaca_id' => 'required|exists:butacas,id', // Se asegura de que la butaca exista
        ]);
        
        // Verificar si la butaca ya está reservada o confirmada
        $reservaExistente = Reserva::where('movie_session_id', $request->movie_session_id)
            ->where('butaca_id', $request->butaca_id)
            ->first();
    
        if ($reservaExistente) {
            return response()->json(['message' => 'La butaca ya está reservada o confirmada'], 400);
        }
    
        // Obtener la butaca seleccionada
        $butaca = Butaca::find($request->butaca_id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404); // Añadimos validación para asegurarnos de que la butaca existe
        }
    
        // Obtener el precio original de la butaca
        $precioOriginal = $butaca->precio;
    
        // Obtener la sesión para verificar si es "Día del Espectador"
        $sesion = MovieSession::find($request->movie_session_id);
        $esDiaEspectador = $sesion->dia_espectador;
    
        // Determinar el descuento basado en el "Día del Espectador"
        $descuento = $esDiaEspectador == 1 ? 2 : 0;
    
        // Calcular el precio ajustado
        $precioFinal = $precioOriginal - $descuento;
    
        // Crear la nueva reserva con estado "reservada" y guardar el precio ajustado
        $reserva = Reserva::create([
            'user_id' => $request->user_id,
            'movie_session_id' => $request->movie_session_id,
            'butaca_id' => $request->butaca_id,
            'estado' => 'reservada',
            'precio' => $precioFinal, // Guardamos el precio ajustado
        ]);
    
        // Devolver la información de la reserva junto con el precio ajustado
        return response()->json([
            'reserva' => $reserva,
            'precio' => $precioFinal, // Precio ajustado
            'butaca' => $butaca,
        ], 201);
    }
    

    public function confirmarButaca($session_id, $butaca_id)
    {
        // Agregar depuración para ver los valores de session_id y butaca_id
        \Log::info("Session ID: $session_id, Butaca ID: $butaca_id");
    
        $reserva = Reserva::where('movie_session_id', $session_id)
            ->where('butaca_id', $butaca_id)
            ->first();
    
        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }
    
        if ($reserva->estado === 'confirmada') {
            return response()->json(['message' => 'La butaca ya está confirmada'], 400);
        }
    
        // Confirmar la reserva
        $reserva->estado = 'confirmada';
        $reserva->save();
    
        return response()->json($reserva, 200);
    }
    

    // Quitar la confirmación (volver a "reservada" o eliminar la reserva)
    public function vaciarButaca($session_id, $butaca_id)
    {
        // Buscar la reserva existente
        $reserva = Reserva::where('movie_session_id', $session_id)
            ->where('butaca_id', $butaca_id)
            ->first();

        if (!$reserva) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        // Si la reserva está confirmada, cambiamos su estado a "reservada"
        if ($reserva->estado === 'confirmada') {
            $reserva->estado = 'reservada';
            $reserva->save();
            return response()->json($reserva, 200);
        }

        // Si la reserva ya está en "reservada" o no es válida, la eliminamos
        $reserva->delete();
        return response()->json(['message' => 'Reserva eliminada correctamente'], 200);
    }
    
    public function liberarButaca($session_id, $butaca_id)
    {
        // Buscar la reserva de la butaca en la sesión dada
        $reserva = Reserva::where('movie_session_id', $session_id)
            ->where('butaca_id', $butaca_id)
            ->first();

        // Si no existe la reserva, devolver error
        if (!$reserva) {
            return response()->json(['message' => 'No se encontró una reserva para esta butaca'], 404);
        }

        // Eliminar la reserva para liberar la butaca
        $reserva->delete();

        return response()->json(['message' => 'Butaca liberada con éxito'], 200);
    }

    
    // Función auxiliar para determinar si es "Día del Espectador"
    private function esDiaEspectador($fecha)
    {
        $diasEspectador = ['Wednesday']; // Puedes modificarlo según las reglas de negocio
        return in_array(date('l', strtotime($fecha)), $diasEspectador);
    }
    

    // Método para verificar si el usuario está seleccionando una butaca
    private function isUserSelecting($butaca_id, $user_id)
    {
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

    public function show($id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }
    
        return response()->json([
            'butaca' => $butaca,
            'precio' => $butaca->precio, // Mostramos el precio de la butaca
        ]);
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
