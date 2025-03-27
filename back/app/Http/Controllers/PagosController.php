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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

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
            $butacasConInfo = [];

            // Nombre único para el PDF
            $pdfFileName = "comprobante_{$request->nombre}_{$request->apellido}_" . time() . ".pdf";
            $pdfPath = "public/{$pdfFileName}";

            foreach ($request->compra_id as $compraId) {
                $compra = Compra::find($compraId);
                if ($compra) {
                    $compra->estado = 'pagado';
                    $compra->save();

                    Reserva::where('compra_id', $compraId)->delete();

                    $mensajes[] = "Pago realizado con éxito para la compra ID: $compraId.";
                    $compras[] = $compra;

                    $butacaIds = json_decode($compra->butaca_ids, true);
                    $butacasCompra = Butaca::whereIn('id', $butacaIds)->get();
                    $sessionCompra = MovieSession::with('movie')->find($compra->movie_session_id);

                    foreach ($butacasCompra as $butaca) {
                        $butacaArray = $butaca->toArray();
                        $butacaArray['session'] = $sessionCompra;
                        $butacaArray['movie'] = $sessionCompra->movie;
                        
                        $codigoUnico = Str::uuid()->toString();
                        $butacaArray['codigo_unico'] = $codigoUnico;

                        // URL pública del PDF (usando storage)
                        $pdfUrl = Storage::url($pdfFileName);
                        
                        // Generar QR que apunte directamente al PDF
                        $qrCode = QrCode::format('svg')
                            ->size(200)
                            ->generate(url($pdfUrl));
                        
                        $butacaArray['qr_code'] = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
                        $butacaArray['qr_url'] = url($pdfUrl); // URL completa para el QR
                        
                        $butacasConInfo[] = $butacaArray;
                    }
                }
            }

            $precioTotal = collect($butacasConInfo)->sum('precio');

            $data = [
                'nombre' => $request->input('nombre'),
                'apellido' => $request->input('apellido'),
                'email' => $request->input('email'),
                'compras' => $compras,
                'butacas' => $butacasConInfo,
                'precio_total' => $precioTotal,
                'pdf_filename' => $pdfFileName // Añade esta línea

            ];

            // Generar y guardar el PDF
            $pdf = Pdf::loadView('pdf.confirmacion', $data);
            $pdf->save(storage_path("app/{$pdfPath}"));

            // Enviar el correo
            try {
                Mail::to($request->email)->send(new HelloMail(
                    $request->nombre,
                    $request->apellido,
                    $pdfFileName
                ));
                
                return response()->json([
                    'message' => 'Pago procesado con éxito y correo enviado con PDF adjunto.',
                    'pdf_url' => url(Storage::url($pdfFileName)),
                    'qr_url' => url(Storage::url($pdfFileName)) // Misma URL para el QR
                ], 200);
                
            } catch (\Exception $e) {
                Log::error("Error al enviar el correo: " . $e->getMessage());
                return response()->json([
                    'message' => 'Pago procesado pero hubo un error al enviar el correo.',
                    'error' => $e->getMessage(),
                    'pdf_url' => url(Storage::url($pdfFileName)),
                    'qr_url' => url(Storage::url($pdfFileName))
                ], 200);
            }
        }
    }

    // Método para descargar el PDF
    public function downloadPDF($filename)
    {
        $path = storage_path('app/public/' . basename($filename));
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="entrada_cine.pdf"'
        ]);
    }

    // Método para verificar el código QR
    public function verificarQR(Request $request)
    {
        $codigo = $request->input('codigo');
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $pelicula = $request->input('pelicula');
        $fecha = $request->input('fecha');
        $hora = $request->input('hora');
        $asiento = $request->input('asiento');
        $precio = $request->input('precio');
    
        // Verificación en la base de datos (implementa tu lógica)
        $valido = true; 
    
        return view('verificacion.qr', [
            'valido' => $valido,
            'codigo' => $codigo,
            'cliente' => $nombre . ' ' . $apellido,
            'pelicula' => $pelicula,
            'fecha' => $fecha,
            'hora' => $hora,
            'asiento' => $asiento,
            'precio' => $precio,
            'pdf_url' => url(Storage::url("comprobante_{$nombre}_{$apellido}_*.pdf"))
        ]);
    }

    // Método para validar el código QR (ejemplo)
    private function validarCodigoQR($codigo)
    {
        // Implementa tu lógica de validación
        // Por ejemplo, verificar en la base de datos
        return true; // o false según corresponda
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