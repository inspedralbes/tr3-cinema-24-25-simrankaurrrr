<?php

namespace App\Http\Controllers;

use App\Models\Entrada;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    // Método para obtener todas las entradas disponibles
    public function index()
    {
        $entradas = Entrada::all();
        return response()->json($entradas);
    }
}
