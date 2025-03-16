<?php

namespace App\Http\Controllers;

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
    
                // Obtener el precio de la butaca y aplicar el descuento si corresponde
                $precioOriginal = $butaca->precio;
                $precioConDescuento = $precioOriginal - $descuento; // Descontamos 2 euros si es "Día del Espectador"
    
                $resultado[] = [
                    'butaca_id' => $butaca->id,
                    'fila' => $butaca->fila,
                    'columna' => $butaca->columna,
                    'estado' => $estado,
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
    $user = auth()->user();

    // Validación de la solicitud
    $request->validate([
        'movie_session_id' => 'required|exists:movie_sessions,id',
        'butaca_id' => 'required|exists:butacas,id',
    ]);

    // Verificar si ya existe una compra en proceso para este usuario
    $compra = Compra::where('user_id', $user->id)
        ->where('movie_session_id', $request->movie_session_id)
        ->where('estado', 'en_proceso')
        ->first();

    // Si no existe una compra en proceso, crear una nueva compra
    if (!$compra) {
        $compra = new Compra();
        $compra->user_id = $user->id;
        $compra->butaca_id = $request->butaca_id;
        $compra->movie_session_id = $request->movie_session_id;
        $compra->ticket_quantity = 1;
        $compra->total_amount = 6; // Precio predeterminado
        $compra->estado = 'en_proceso';
        $compra->save();
    }

    // Verificar si la butaca ya está reservada
    $reservaExistente = Reserva::where('movie_session_id', $request->movie_session_id)
        ->where('butaca_id', $request->butaca_id)
        ->first();

    if ($reservaExistente) {
        return response()->json(['message' => 'La butaca ya está reservada'], 400);
    }

    // Crear una nueva reserva con estado 'procesando' y asociarla con la compra
// Crear una nueva reserva con estado 'procesando' y asociarla con la compra
$reserva = new Reserva();
$reserva->compra_id = $compra->id;  // Asociamos la compra con la reserva
$reserva->user_id = $user->id;
$reserva->movie_session_id = $request->movie_session_id;
$reserva->butaca_id = $request->butaca_id;
$reserva->estado = 'procesando';
$reserva->precio = 6;  // Precio predeterminado
$reserva->save();

    return response()->json([
        'message' => 'Reserva en proceso. Por favor, complete el pago.',
        'reserva' => $reserva, // Datos de la reserva
        'compra_id' => $compra->id // Asegúrate de devolver el compra_id
    ], 200);
}


    // Actualizado: Confirmar directamente la compra cuando se ingresen los datos de pago
    public function confirmarCompra(Request $request)
    {
        $user = auth()->user(); // Obtener el usuario autenticado
    
        // Validación de la solicitud
        $request->validate([
            'reserva_id' => 'required|exists:reservas,id',  // Aseguramos que la reserva existe
            'numero_tarjeta' => 'required|string',
            'fecha_vencimiento' => 'required|string',
            'cvv' => 'required|string',
        ]);

        // Obtener la reserva por su ID
        $reserva = Reserva::find($request->reserva_id);

        // Si la reserva no existe o no está en proceso, devolver error
        if (!$reserva || $reserva->estado !== 'procesando') {
            return response()->json(['message' => 'La reserva no está disponible para compra'], 400);
        }

        // Simulación de validación de pago
        $pagoExitoso = $this->validarPagoSimulado($request->numero_tarjeta, $request->cvv, $request->fecha_vencimiento);

        // Crear un registro en la tabla 'pagos'
        $pago = new Pagos();
        $pago->reserva_id = $reserva->id;
        $pago->numero_tarjeta = $request->numero_tarjeta;
        $pago->fecha_vencimiento = $request->fecha_vencimiento;
        $pago->cvv = $request->cvv;
        $estadoPago = $pagoExitoso ? 'completado' : 'fallido';
        $pago->estado_pago = $estadoPago;
        $pago->save();

        // Actualizar el estado de la reserva según el pago
        if ($pagoExitoso) {
            // Si el pago es exitoso, marcar la reserva como "confirmado" y "pagado"
            $reserva->estado = 'confirmado'; // Cambio el estado a "confirmado"
            $reserva->estado_compra = 'pagado'; // Estado de compra "pagado"
            $reserva->save();

            return response()->json(['message' => 'Pago exitoso, compra confirmada!'], 200);
        } else {
            // Si el pago falla, marcar la reserva como "reservada" y estado de compra "fallido"
            $reserva->estado = 'reservada'; 
            $reserva->estado_compra = 'fallido';
            $reserva->save();

            return response()->json(['message' => 'Pago fallido, reserva no confirmada.'], 400);
        }
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

    // Obtener todas las reservas en estado "procesando" del usuario
    $reservas = Reserva::where('user_id', $user->id)
                        ->where('estado', 'procesando')
                        ->get();

    $carrito = [];

    foreach ($reservas as $reserva) {
        $butaca = Butaca::find($reserva->butaca_id);
        $compra = Compra::where('id', $reserva->compra_id)->first(); // Obtener la compra asociada

        if ($butaca && $compra) {
            $carrito[] = [
                'compra_id' => $compra->id, // Ahora se obtiene correctamente la compra_id
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

    

}
