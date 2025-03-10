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
        // Obtener usuarios (asegurándonos de que haya al menos dos usuarios)
        $usuarios = User::all();
        if ($usuarios->count() < 2) {
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

        // Seleccionar 2 butacas disponibles para cada sesión
        $butacasSesion1 = Butaca::whereNotIn('id', Reserva::pluck('butaca_id')->toArray())
            ->where('columna', '<=', 10) // Asegurarse de que las butacas estén dentro de la fila/columna permitidas
            ->where('fila', '!=', 'F') // Suponiendo que la fila F es VIP y no la consideramos
            ->limit(1)
            ->get();

        $butacasSesion2 = Butaca::whereNotIn('id', Reserva::pluck('butaca_id')->toArray())
            ->where('columna', '<=', 10)
            ->where('fila', '!=', 'F')
            ->limit(1)
            ->get();

        // Comprobar que tenemos butacas disponibles
        if ($butacasSesion1->isEmpty() || $butacasSesion2->isEmpty()) {
            $this->command->info('No hay suficientes butacas disponibles.');
            return;
        }

        // Crear reservas para cada sesión
        // Usuario 1 - Sesión 1
        Reserva::create([
            'user_id' => $usuarios[0]->id,
            'movie_session_id' => $sesion1->id,
            'butaca_id' => $butacasSesion1->first()->id, // Usamos la primera butaca
        ]);

        // Usuario 2 - Sesión 2
        Reserva::create([
            'user_id' => $usuarios[1]->id,
            'movie_session_id' => $sesion2->id,
            'butaca_id' => $butacasSesion2->first()->id, // Usamos la primera butaca
        ]);

        $this->command->info('Se han creado 2 reservas en sesiones diferentes de películas diferentes.');
    }
}
