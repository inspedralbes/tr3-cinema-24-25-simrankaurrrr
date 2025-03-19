<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Models\Butaca;
use App\Models\Reserva;
use App\Models\MovieSession;
use App\Models\User;
use App\Models\Compra;

use App\Models\Pagos; // Asegúrate de importar el modelo de Pagos
use Illuminate\Http\Request;

class ButacaController extends Controller
{
    public function obtenerButacasPorSesion($session_id, Request $request)
    {
        // Obtener todas las butacas de la sesión
        $butacas = Butaca::all();
    
        // Obtener las compras pagadas
        $comprasPagadas = Compra::where('movie_session_id', $session_id)
            ->where('estado', 'pagado')
            ->pluck('butaca_ids')->toArray();
    
        // Obtener las reservas en proceso (esto indica que aún no han sido pagadas)
        $reservasEnProceso = Reserva::where('movie_session_id', $session_id)
            ->where('estado', 'en_proceso')
            ->pluck('butaca_ids')->toArray();
    
        // Convertir JSON a arrays
        $comprasPagadas = collect($comprasPagadas)->map(fn($c) => json_decode($c, true))->flatten()->toArray();
        $reservasEnProceso = collect($reservasEnProceso)->map(fn($r) => json_decode($r, true))->flatten()->toArray();
    
        $resultado = [];
    
        foreach ($butacas as $butaca) {
            $estado = 'disponible';
    
            if (in_array($butaca->id, $comprasPagadas)) {
                $estado = 'comprado';
            } elseif (in_array($butaca->id, $reservasEnProceso)) {
                $estado = 'reservado';
            }
    
            $resultado[] = [
                'butaca_id' => $butaca->id,
                'fila' => $butaca->fila,
                'columna' => $butaca->columna,
                'estado' => $estado,
                'vip' => $butaca->vip,
                'precio' => $butaca->precio,
            ];
        }
    
        return response()->json($resultado);
    }
    
    public function reservarButaca(Request $request)
{
    $user = auth()->user();
    
    // Validación de la solicitud
    $request->validate([
        'movie_session_id' => 'required|exists:movie_sessions,id',
        'butaca_ids' => 'required|array',
        'butaca_ids.*' => 'exists:butacas,id',
    ]);

    // Verificar cuántas entradas ha comprado el usuario para esta sesión
    $comprasExistentes = Compra::where('user_id', $user->id)
        ->where('movie_session_id', $request->movie_session_id)
        ->get();
    
    $entradasCompradas = $comprasExistentes->sum('ticket_quantity');

    // Verificar si el usuario ya tiene 10 entradas para esta sesión
    if ($entradasCompradas + count($request->butaca_ids) > 10) {
        return response()->json(['message' => 'No puedes comprar más de 10 entradas para esta sesión'], 400);
    }

    // Crear las compras y reservas
    foreach ($request->butaca_ids as $butaca_id) {
        $butaca = Butaca::find($butaca_id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada.'], 404);
        }

        // Crear la compra
        $compra = new Compra();
        $compra->user_id = $user->id;
        $compra->movie_session_id = $request->movie_session_id;
        $compra->ticket_quantity = 1;
        $compra->total_amount = $butaca->precio; // Precio dinámico
        $compra->estado = 'en_proceso';
        $compra->butaca_ids = json_encode([$butaca_id]);
        $compra->save();

        // Crear la reserva
        $reserva = new Reserva();
        $reserva->compra_id = $compra->id;
        $reserva->user_id = $user->id;
        $reserva->movie_session_id = $request->movie_session_id;
        $reserva->butaca_ids = json_encode([$butaca_id]);
        $reserva->estado = 'en_proceso'; // Cambiado de 'procesando' a 'en_proceso' para coherencia
        $reserva->precio = $butaca->precio;
        $reserva->save();
    }

    return response()->json([
        'message' => 'Reservas en proceso. Por favor, complete el pago.',
    ], 200);
}


public function confirmarCompra(Request $request)
{
    $user = auth()->user();

    // Validación de la solicitud
    $request->validate([
        'reserva_ids' => 'required|array',
        'reserva_ids.*' => 'exists:reservas,id',
        'numero_tarjeta' => 'required|string',
        'fecha_vencimiento' => 'required|string',
        'cvv' => 'required|string',
    ]);

    // Simulación de validación de pago
    $pagoExitoso = $this->validarPagoSimulado($request->numero_tarjeta, $request->cvv, $request->fecha_vencimiento);

    // Registrar el pago
    $pago = new Pagos();
    $pago->numero_tarjeta = $request->numero_tarjeta;
    $pago->fecha_vencimiento = $request->fecha_vencimiento;
    $pago->cvv = $request->cvv;
    $pago->estado_pago = $pagoExitoso ? 'completado' : 'fallido';
    $pago->save();

    if ($pagoExitoso) {
        DB::beginTransaction();
        try {
            foreach ($request->reserva_ids as $reserva_id) {
                $reserva = Reserva::find($reserva_id);
                if ($reserva && $reserva->estado === 'en_proceso') {
                    // Actualizar la compra a "pagado"
                    $compra = Compra::find($reserva->compra_id);
                    if ($compra) {
                        $compra->estado = 'pagado';
                        $compra->save();
                    }

                    // Eliminar la reserva porque ya fue pagada
                    $reserva->estado = 'pagada'; // Para mantener el registro en la base de datos
                    $reserva->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Compra confirmada y reserva eliminada.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error en la confirmación de la compra.'], 500);
        }
    }

    return response()->json(['message' => 'Pago fallido.'], 400);
}
    // Función para simular la validación del pago
    private function validarPagoSimulado($numeroTarjeta, $cvv, $fechaVencimiento)
    {
        // Simulación de validación de tarjeta (puedes mejorar esta lógica según tus necesidades)
        if (strlen($numeroTarjeta) == 16 && strlen($cvv) == 3 && $this->validarFechaVencimiento($fechaVencimiento)) {
            return true;  // Pago simulado exitoso
        }
        
        return false; // Pago fallido
    }

    // Validar la fecha de vencimiento
    private function validarFechaVencimiento($fechaVencimiento)
    {
        $currentDate = date('m/y'); // Fecha actual en formato mes/año
        return $fechaVencimiento >= $currentDate;  // Asegurarse de que la fecha no haya expirado
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
    public function verCarrito(Request $request)
    {
        $user = auth()->user();
    
        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }
    
        // Obtener todas las reservas en estado "en_proceso" o "procesando" del usuario
        $reservas = Reserva::where('user_id', $user->id)
                            ->whereIn('estado', ['en_proceso', 'procesando']) // Incluir ambos estados
                            ->with(['butaca', 'movieSession.movie']) // Cargar relaciones
                            ->get();
    
        $carrito = [];
    
        foreach ($reservas as $reserva) {
            // Decodificar butaca_ids (que está en formato JSON)
            $butacaIds = json_decode($reserva->butaca_ids, true);
    
            // Obtener las butacas asociadas
            $butacas = Butaca::whereIn('id', $butacaIds)->get();
    
            foreach ($butacas as $butaca) {
                $carrito[] = [
                    'compra_id' => $reserva->compra_id,
                    'reserva_id' => $reserva->id,
                    'butaca_id' => $butaca->id,
                    'fila' => $butaca->fila,
                    'columna' => $butaca->columna,
                    'precio' => $reserva->precio,
                    'session_id' => $reserva->movie_session_id,
                    'estado' => $reserva->estado,
                    'nombre_pelicula' => $reserva->movieSession->movie->title,
                ];
            }
        }
    
        return response()->json($carrito);
    }

public function eliminarReserva($reserva_id)
{
    $reserva = Reserva::find($reserva_id);

    if ($reserva) {
        $reserva->delete();
        return response()->json(['message' => 'Reserva eliminada con éxito']);
    } else {
        return response()->json(['message' => 'Reserva no encontrada'], 404);
    }
}

public function verEstadoSesion($session_id)
{
    // Obtener todas las butacas asociadas a la sesión
    $butacas = Butaca::all();

    // Obtener reservas y compras para la sesión
    $reservas = Reserva::where('movie_session_id', $session_id)->get();
    $compras = Compra::where('movie_session_id', $session_id)->get();

    $resultado = [];

    foreach ($butacas as $butaca) {
        // Estado por defecto
        $estado = 'disponible';

        // Buscar si la butaca está en una reserva "en_proceso"
        $reserva = $reservas->firstWhere('butaca_ids', 'like', '%"'.$butaca->id.'"%');
        if ($reserva && $reserva->estado === 'en_proceso') {
            $estado = 'reservado';  // Butaca reservada pero no pagada
        }

        // Buscar si la butaca ya fue comprada y pagada
        foreach ($compras as $compra) {
            $butaca_ids = json_decode($compra->butaca_ids, true);
            if (in_array($butaca->id, $butaca_ids) && $compra->estado === 'pagado') {
                $estado = 'comprado';  // Butaca ya comprada y pagada
                break;
            }
        }

        // Agregar al resultado
        $resultado[] = [
            'butaca_id' => $butaca->id,
            'fila' => $butaca->fila,
            'columna' => $butaca->columna,
            'estado' => $estado,
            'vip' => $butaca->vip,
            'precio' => $butaca->precio
        ];
    }

    return response()->json($resultado);
}

public function obtenerResumenSesion(Request $request)
{
    // Obtener los parámetros de fecha y hora
    $sessionDate = $request->query('session_date');  // Fecha de la sesión (ej. 2025-03-19)
    $sessionTime = $request->query('session_time');  // Hora de la sesión (ej. 16:00:00)

    // Buscar la sesión de la película
    $movieSession = MovieSession::whereDate('session_date', $sessionDate)
                                ->whereTime('session_time', $sessionTime)
                                ->first();

    // Si no se encuentra la sesión, devolver un error
    if (!$movieSession) {
        return response()->json(['message' => 'Sesión no encontrada para esa fecha y hora'], 404);
    }

    // Obtener todas las butacas de la sesión
    $butacas = Butaca::all();

    // Obtener las compras pagadas para esta sesión
    $comprasPagadas = Compra::where('movie_session_id', $movieSession->id)
        ->where('estado', 'pagado')
        ->pluck('butaca_ids')->toArray();

    // Obtener las reservas en proceso para esta sesión
    $reservasEnProceso = Reserva::where('movie_session_id', $movieSession->id)
        ->where('estado', 'en_proceso')
        ->pluck('butaca_ids')->toArray();

    // Convertir JSON a arrays
    $comprasPagadas = collect($comprasPagadas)->map(fn($c) => json_decode($c, true))->flatten()->toArray();
    $reservasEnProceso = collect($reservasEnProceso)->map(fn($r) => json_decode($r, true))->flatten()->toArray();

    $resultado = [];
    $entradasNormal = 0;
    $entradasVIP = 0;
    $recaudacionNormal = 0;
    $recaudacionVIP = 0;
    $recaudacionTotal = 0;

    foreach ($butacas as $butaca) {
        $estado = 'disponible';

        // Verificar el estado de la butaca
        if (in_array($butaca->id, $comprasPagadas)) {
            $estado = 'comprado';
            // Calcular recaudación y entradas por tipo
            if ($butaca->vip) {
                $entradasVIP++;
                $recaudacionVIP += $butaca->precio;
            } else {
                $entradasNormal++;
                $recaudacionNormal += $butaca->precio;
            }
            $recaudacionTotal += $butaca->precio;
        } elseif (in_array($butaca->id, $reservasEnProceso)) {
            $estado = 'reservado';
        }

        $resultado[] = [
            'butaca_id' => $butaca->id,
            'fila' => $butaca->fila,
            'columna' => $butaca->columna,
            'estado' => $estado,
            'vip' => $butaca->vip,
            'precio' => $butaca->precio,
        ];
    }

    // Añadir los cálculos al resultado final
    $resultadoResumen = [
        'entradas_normal' => $entradasNormal,
        'entradas_vip' => $entradasVIP,
        'recaudacion_normal' => $recaudacionNormal,
        'recaudacion_vip' => $recaudacionVIP,
        'recaudacion_total' => $recaudacionTotal,
        'butacas' => $resultado,
    ];

    return response()->json($resultadoResumen);
}

}
