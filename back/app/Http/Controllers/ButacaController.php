<?php
namespace App\Http\Controllers;

use App\Models\Butaca;
use App\Models\MovieSession;
use Illuminate\Http\Request;

class ButacaController extends Controller
{
    public function index()
    {
        // Obtener solo las butacas ocupadas
        $butacas = Butaca::where('ocupada', true)->get();
        return response()->json($butacas);
    }
      // Método para obtener las butacas por sesión
      public function getButacasPorSesion($session_id)
      {
          $butacas = Butaca::where('movie_session_id', $session_id)->get();
          return response()->json($butacas);
      }

    public function store(Request $request)
    {
        $request->validate([
            'movie_session_id' => 'required|exists:movie_sessions,id',
            'fila' => 'required|string|in:A,B,C,D,E,F,G,H,I,J,K,L', // Restricción de las filas A-L
            'columna' => 'required|integer|min:1|max:10', // Restricción de las columnas 1-10
            'is_vip' => 'boolean',
        ]);

        // Validación para que las butacas VIP solo estén en la fila F
        if ($request->is_vip && $request->fila !== 'F') {
            return response()->json(['message' => 'Solo la fila F puede ser VIP.'], 400);
        }

        // Crear la butaca
        $butaca = Butaca::create([
            'movie_session_id' => $request->movie_session_id,
            'fila' => $request->fila,
            'columna' => $request->columna,
            'ocupada' => false,
            'is_vip' => $request->is_vip,
        ]);

        return response()->json($butaca, 201);
    }

    public function show($id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }
        return response()->json($butaca);
    }

    public function update(Request $request, $id)
    {
        $butaca = Butaca::find($id);
        if (!$butaca) {
            return response()->json(['message' => 'Butaca no encontrada'], 404);
        }

        $request->validate([
            'ocupada' => 'boolean',
            'is_vip' => 'boolean',
        ]);

        // Evitar cambiar VIP en filas no permitidas
        if ($request->has('is_vip') && $request->is_vip && $butaca->fila !== 'F') {
            return response()->json(['message' => 'Solo la fila F puede ser VIP.'], 400);
        }

        $butaca->update($request->only(['ocupada', 'is_vip']));
        return response()->json($butaca);
    }

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
