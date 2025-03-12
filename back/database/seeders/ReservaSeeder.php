<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reserva;
use App\Models\MovieSession;
use App\Models\Butaca;
use App\Models\User;

class ReservaSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $movieSessions = MovieSession::all();
        $butacas = Butaca::all();
        
        foreach ($movieSessions as $movieSession) {
            foreach ($users as $user) {
                // Comprobar si es un día del espectador
                $esDiaEspectador = $movieSession->dia_espectador;

                // Obtener la butaca seleccionada
                $butaca = $this->getButacaForReserva($butacas);

                // Obtener el precio original de la butaca
                $precioOriginal = $butaca->precio;

                // Calcular el precio ajustado basado en "Día del Espectador"
                $descuento = $esDiaEspectador == 1 ? 2 : 0;
                $precioFinal = $precioOriginal - $descuento;

                // Crear la reserva con el precio ajustado
                Reserva::create([
                    'user_id' => $user->id,
                    'movie_session_id' => $movieSession->id,
                    'butaca_id' => $butaca->id,
                    'estado' => 'reservada', // Estado de la reserva
                    'precio' => $precioFinal, // Guardar el precio ajustado
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getButacaForReserva($butacas)
    {
        // Aquí puedes definir una lógica para seleccionar una butaca aleatoria
        return $butacas->random(); // Asignar una butaca aleatoria de la lista
    }
}
