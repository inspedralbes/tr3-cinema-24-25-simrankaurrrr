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
        $movies = Movie::all(); 

        $startDate = Carbon::create(2025, 4, 1);
        
        $times = ['16:00:00', '18:00:00', '20:00:00'];
        $timeIndex = 0;

        foreach ($movies as $index => $movie) {
            $sessionDate = $startDate->copy()->addDays($index);
            
            $sessionTime = $times[$timeIndex % 3];
            $timeIndex++;
            
            MovieSession::create([
                'movie_id' => $movie->id,
                'session_time' => $sessionTime,
                'session_date' => $sessionDate->toDateString(),
                'dia_espectador' => $sessionTime == '18:00:00', 
            ]);
        }
    }
}