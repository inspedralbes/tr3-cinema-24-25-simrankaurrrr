<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run()
    {
        Movie::create([
            'title' => 'Película de Acción',
            'sinopsis' => 'Una película llena de acción y aventuras.',
            'duracion' => '120 minutos',
            'director' => 'John Doe',
            'actores' => 'Actor A, Actor B',
            'año' => 2025,
            'genero' => 'Acción',
            'poster_url' => 'https://example.com/poster.jpg',
            'trailer_url' => 'https://youtube.com/trailer1',
            'idioma' => 'Inglés',
            'subtitulos' => true,
            'formato' => 'IMAX',
            'disponible_en_streaming' => true,
        ]);

        Movie::create([
            'title' => 'Película de Comedia',
            'sinopsis' => 'Una película de comedia para toda la familia.',
            'duracion' => '100 minutos',
            'director' => 'Jane Smith',
            'actores' => 'Actor C, Actor D',
            'año' => 2024,
            'genero' => 'Comedia',
            'poster_url' => 'https://example.com/poster2.jpg',
            'trailer_url' => 'https://youtube.com/trailer2',
            'idioma' => 'Español',
            'subtitulos' => false,
            'formato' => '2D',
            'disponible_en_streaming' => false,
        ]);
    }
}
