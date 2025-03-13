<?php
namespace Database\Seeders;

use App\Models\MovieSession;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $movie = Movie::first();  // Obtiene la primera película
        $movie2 = Movie::skip(1)->first();  // Obtiene la segunda película (salta el primer registro)

        MovieSession::create([
            'movie_id' => $movie->id,
            'session_time' => '16:00:00',  // Ejemplo de hora
            'session_date' => '2025-03-10',
            'dia_espectador' => false,
        ]);
        MovieSession::create([
            'movie_id' => $movie->id,
            'session_time' => '18:00:00',
            'session_date' => '2025-03-10',
            'dia_espectador' => true,  // Es un Día del espectador
        ]);
        MovieSession::create([
            'movie_id' => $movie2->id,
            'session_time' => '21:00:00',
            'session_date' => '2025-03-10',
            'dia_espectador' => true,  // Es un Día del espectador
        ]);
    }
}

