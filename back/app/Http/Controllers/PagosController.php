<?php
namespace App\Http\Controllers;
use App\Models\Reserva;  // Asegúrate de tener este modelo importado

use Illuminate\Http\Request;
use App\Models\Pagos;  // Correcta importación del modelo Pagos
use App\Models\Compra;  // Modelo de Compra
use Illuminate\Support\Facades\Validator;

class PagosController extends Controller
{
    public function realizarPago(Request $request)
    {
        // Validar los datos del pago
        $validator = Validator::make($request->all(), [
            'numero_tarjeta' => 'required|string|max:16',
            'fecha_vencimiento' => 'required|string|max:5',
            'cvv' => 'required|string|max:3',
            'compra_id' => 'required|exists:compras,id', // Aquí se espera compra_id
        ]);
    
        // Si la validación falla, devolver el error
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
    
        // Simulación de validación de pago (si el pago es exitoso o no)
        $pagoExitoso = $this->validarPagoSimulado($request->numero_tarjeta, $request->cvv, $request->fecha_vencimiento);
    
        // Crear el pago en la base de datos
        $pago = new Pagos();
        $pago->numero_tarjeta = $request->numero_tarjeta;
        $pago->fecha_vencimiento = $request->fecha_vencimiento;
        $pago->cvv = $request->cvv;
        $pago->compra_id = $request->compra_id; // Asociar el pago a la compra
    
        // Si el pago fue exitoso, guardamos el pago en la base de datos
        $pago->save();
    
        // Si el pago fue exitoso, actualizamos la compra y eliminamos la reserva
        if ($pagoExitoso) {
            // Actualizar la compra como pagada
            $compra = Compra::find($request->compra_id);
            $compra->estado = 'pagado'; // Cambiar estado a 'pagado'
            $compra->save();
    
            // Eliminar las reservas asociadas a esta compra
            $reservas = Reserva::where('compra_id', $request->compra_id)->get();
            foreach ($reservas as $reserva) {
                $reserva->delete(); // Eliminar cada reserva
            }
    
            return response()->json([
                'message' => 'Pago realizado con éxito. Compra confirmada y reservas eliminadas.',
                'pago' => $pago
            ], 200);
        } else {
            // Si el pago falla, devolver un mensaje de error
            return response()->json([
                'message' => 'Pago fallido. Por favor, intente nuevamente.'
            ], 400);
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
    
    // Función para validar la fecha de vencimiento de la tarjeta (simulada)
    private function validarFechaVencimiento($fechaVencimiento)
    {
        // Lógica para verificar si la fecha de vencimiento es válida
        $hoy = \Carbon\Carbon::now();
        $fechaVencimiento = \Carbon\Carbon::createFromFormat('m/y', $fechaVencimiento);
    
        return $fechaVencimiento->isFuture(); // Verificar si la fecha es futura
    }
}    
