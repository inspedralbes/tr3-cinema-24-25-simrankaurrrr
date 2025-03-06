<?php
namespace Database\Seeders;

use App\Models\Butaca;
use App\Models\MovieSession;
use Illuminate\Database\Seeder;

class ButacaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movieSession = MovieSession::first();  // Puedes cambiar por una sesión específica

        $filas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];  // Las filas A-L

        foreach ($filas as $fila) {
            $ocupadas = rand(3, 4);  // Seleccionamos aleatoriamente 3 o 4 butacas ocupadas en cada fila
            
            $butacas_ocupadas = array_rand(range(1, 10), $ocupadas); // Seleccionamos aleatoriamente las butacas ocupadas
            
            for ($columna = 1; $columna <= 10; $columna++) {
                // Determinamos si la butaca es ocupada
                $ocupada = in_array($columna, (array) $butacas_ocupadas) ? true : false;
                
                // Para la fila F, marcamos las butacas como VIP
                $isVip = $fila === 'F' ? true : false;

                Butaca::create([
                    'movie_session_id' => $movieSession->id,
                    'fila' => $fila,
                    'columna' => $columna,
                    'ocupada' => $ocupada,  // Asignamos si está ocupada
                    'is_vip' => $isVip,  // Si es la fila F, marcarla como VIP
                ]);
            }
        }
    }
}
