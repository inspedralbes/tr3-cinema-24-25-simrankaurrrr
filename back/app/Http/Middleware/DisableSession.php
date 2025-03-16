<?php
// app/Http/Middleware/DisableSession.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableSession
{
    public function handle(Request $request, Closure $next)
    {
        // Desactivar la sesión para esta solicitud
        $request->session()->forget('payload');
        return $next($request);
    }
}
