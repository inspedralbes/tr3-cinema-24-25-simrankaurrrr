<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Pagos;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagosController extends Controller
{
    // Modificación para aceptar pagos individuales y múltiples
    public function realizarPago(Request $request)
    {
        // Validación: Aceptar una compra_id o un array de compra_ids
        $validator = Validator::make($request->all(), [
            'numero_tarjeta' => 'required|string|max:16',
            'fecha_vencimiento' => 'required|string|max:5',
            'cvv' => 'required|string|max:3',
            'compra_id' => 'required|array', // Esperamos un array de compra_ids
            'compra_id.*' => 'exists:compras,id', // Validamos que cada compra_id exista en la base de datos
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
        $pago->compra_id = null; // Se asignará más tarde cuando se procese cada compra

        // Si el pago fue exitoso, procesar las compras
        if ($pagoExitoso) {
            $mensajes = [];
            foreach ($request->compra_id as $compraId) {
                // Procesar cada compra individualmente
                $compra = Compra::find($compraId);
                
                if ($compra) {
                    // Asociar el pago con la compra
                    $pago->compra_id = $compraId;
                    $pago->save();  // Guardar el pago en la base de datos

                    // Actualizar la compra como pagada
                    $compra->estado = 'pagado';
                    $compra->save();

                    // Eliminar las reservas asociadas a esta compra
                    $reservas = Reserva::where('compra_id', $compraId)->get();
                    foreach ($reservas as $reserva) {
                        $reserva->delete();  // Eliminar cada reserva
                    }

                    // Agregar mensaje de éxito
                    $mensajes[] = "Pago realizado con éxito para la compra ID: $compraId.";
                } else {
                    // Si la compra no existe, agregar mensaje de error
                    $mensajes[] = "Compra ID $compraId no encontrada.";
                }
            }

            return response()->json([
                'message' => 'Pagos procesados con éxito.',
                'details' => $mensajes,
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
