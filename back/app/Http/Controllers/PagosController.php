<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Pagos;
use App\Models\Butaca;
use App\Models\MovieSession;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PagosController extends Controller
{
  
    public function realizarPago(Request $request)
{
    // Validación
    $validator = Validator::make($request->all(), [
        'numero_tarjeta' => 'required|string|max:16',
        'fecha_vencimiento' => 'required|string|max:5',
        'cvv' => 'required|string|max:3',
        'compra_id' => 'required|array',
        'compra_id.*' => 'exists:compras,id',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error' => $validator->errors()
        ], 400);
    }

    $pagoExitoso = $this->validarPagoSimulado($request->numero_tarjeta, $request->cvv, $request->fecha_vencimiento);

    if ($pagoExitoso) {
        $mensajes = [];
        $compras = [];
        foreach ($request->compra_id as $compraId) {
            $compra = Compra::find($compraId);
            if ($compra) {
                $compra->estado = 'pagado';
                $compra->save();

                Reserva::where('compra_id', $compraId)->delete();

                $mensajes[] = "Pago realizado con éxito para la compra ID: $compraId.";
                $compras[] = $compra;
            } else {
                $mensajes[] = "Compra ID $compraId no encontrada.";
            }
        }

        // Obtener detalles de las butacas y la sesión
        $butacas = [];
        $session = null;
        foreach ($compras as $compra) {
            $butacaIds = json_decode($compra->butaca_ids, true);
            $butacas = Butaca::whereIn('id', $butacaIds)->get();
            $session = MovieSession::find($compra->movie_session_id);
        }

        $data = [
            'nombre' => $request->input('nombre'),
            'apellido' => $request->input('apellido'),
            'email' => $request->input('email'),
            'compras' => $compras,
            'butacas' => $butacas,
            'session' => $session,
        ];

        $pdf = Pdf::loadView('pdf.confirmacion', $data);
        Log::info('Generando PDF con datos:', ['nombre' => $request->input('nombre'), 'apellido' => $request->input('apellido')]);

        // Guardar temporalmente el PDF
        $pdfPath = storage_path("app/public/comprobante_{$request->nombre}_{$request->apellido}.pdf");
        $pdf->save($pdfPath);

        // Enviar correo con el PDF adjunto
        Mail::to($request->email)->send(new HelloMail(
            $request->nombre,
            $request->apellido,
            $pdfPath
        ));

        return response()->json([
            'message' => 'Pago procesado con éxito y correo enviado con PDF adjunto.',
        ], 200);
    } else {
        return response()->json([
            'message' => 'Pago fallido. Por favor, intente nuevamente.'
        ], 400);
    }
}

    private function validarPagoSimulado($numeroTarjeta, $cvv, $fechaVencimiento)
    {
        if (strlen($numeroTarjeta) == 16 && strlen($cvv) == 3 && $this->validarFechaVencimiento($fechaVencimiento)) {
            return true;
        }
        return false;
    }

    private function validarFechaVencimiento($fechaVencimiento)
    {
        $hoy = \Carbon\Carbon::now();
        $fechaVencimiento = \Carbon\Carbon::createFromFormat('m/y', $fechaVencimiento);

        return $fechaVencimiento->isFuture();
    }
}
