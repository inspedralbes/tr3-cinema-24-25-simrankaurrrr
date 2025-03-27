<?php

namespace Database\Seeders;

use App\Models\MovieSession;
use App\Models\Movie;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MovieSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movies = Movie::all(); // Obtiene todas las películas

        // Fecha de inicio (1 de abril 2025)
        $startDate = Carbon::create(2025, 4, 1);
        
        // Horarios permitidos (en orden rotativo)
        $times = ['16:00:00', '18:00:00', '20:00:00'];
        $timeIndex = 0;

        foreach ($movies as $index => $movie) {
            // Calculamos fecha única para cada película (días consecutivos)
            $sessionDate = $startDate->copy()->addDays($index);
            
            // Seleccionamos el horario correspondiente (rotando entre los 3)
            $sessionTime = $times[$timeIndex % 3];
            $timeIndex++;
            
            // Creamos una única sesión por película
            MovieSession::create([
                'movie_id' => $movie->id,
                'session_time' => $sessionTime,
                'session_date' => $sessionDate->toDateString(),
                'dia_espectador' => $sessionTime == '18:00:00', // Día del espectador solo a las 18:00
            ]);
        }
    }
}