<?php
namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
        ]);
    }
}
