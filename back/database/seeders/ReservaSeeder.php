<?php
namespace Database\Seeders;

use App\Models\Reserva;
use App\Models\MovieSession;
use App\Models\User;
use App\Models\Butaca;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener usuarios (asegurándonos de que haya al menos tres usuarios)
        $usuarios = User::all();
        if ($usuarios->count() < 3) {
            $this->command->info('No hay suficientes usuarios disponibles.');
            return;
        }

        // Obtener sesiones de películas (asegurándonos de que haya al menos dos sesiones de diferentes películas)
        $sesiones = MovieSession::all();
        if ($sesiones->count() < 2) {
            $this->command->info('No hay suficientes sesiones disponibles.');
            return;
        }

        // Seleccionamos dos sesiones diferentes
        $sesion1 = $sesiones[0];  // Primera sesión
        $sesion2 = $sesiones[1];  // Segunda sesión

        // Seleccionar 3 butacas disponibles para cada sesión
        $butacasSesion1 = Butaca::whereNotIn('id', Reserva::pluck('butaca_id')->toArray())
            ->where('columna', '<=', 10) // Asegurarse de que las butacas estén dentro de las columnas permitidas
            ->where('fila', '!=', 'F') // Suponiendo que la fila F es VIP y no la consideramos
            ->limit(3) // Limitamos a 3 para tener 3 tipos de estado
            ->get();

        $butacasSesion2 = Butaca::whereNotIn('id', Reserva::pluck('butaca_id')->toArray())
            ->where('columna', '<=', 10)
            ->where('fila', '!=', 'F')
            ->limit(3)
            ->get();

        // Comprobar que tenemos suficientes butacas disponibles
        if ($butacasSesion1->count() < 3 || $butacasSesion2->count() < 3) {
            $this->command->info('No hay suficientes butacas disponibles.');
            return;
        }

        // Crear reservas para cada sesión:
        
        // Reserva 1 - Ocupada (Roja) -> Usuario 1 (Estado 'reservada')
        Reserva::create([
            'user_id' => $usuarios[0]->id,
            'movie_session_id' => $sesion1->id,
            'butaca_id' => $butacasSesion1[0]->id, // Usamos la primera butaca
            'estado' => 'reservada', // Estado 'reservada'
        ]);

        // Reserva 2 - Seleccionada por el usuario (Verde) -> Usuario 2 (Estado 'confirmada')
        Reserva::create([
            'user_id' => $usuarios[1]->id,
            'movie_session_id' => $sesion2->id,
            'butaca_id' => $butacasSesion2[0]->id, // Usamos la primera butaca
            'estado' => 'confirmada', // Estado 'confirmada'
        ]);

        // Reserva 3 - Disponible (Gris) -> Usuario 3 (Estado 'cancelada' o 'disponible')
        Reserva::create([
            'user_id' => $usuarios[2]->id,
            'movie_session_id' => $sesion1->id,
            'butaca_id' => $butacasSesion1[1]->id, // Usamos la segunda butaca
        ]);

        $this->command->info('Se han creado 3 reservas con diferentes estados: reservada, confirmada y cancelada.');
    }
}
