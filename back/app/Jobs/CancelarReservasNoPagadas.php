<?php

namespace App\Jobs;

use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelarReservasNoPagadas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Obtener reservas con estado "procesando" que tienen más de 5 horas
        $reservasExpiradas = Reserva::where('estado', 'procesando')
            ->where('created_at', '<', Carbon::now()->subHours(5))
            ->get();

        foreach ($reservasExpiradas as $reserva) {
            $reserva->estado = 'cancelada'; // Marcar como cancelada
            $reserva->save();
        }
    }
}
