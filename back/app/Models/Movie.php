<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';

    protected $fillable = [
        'title',
        'sinopsis',
        'duracion',
        'director',
        'actores',
        'año',
        'genero',
        'poster_url',
        'trailer_url',
        'idioma',
        'subtitulos',
        'formato',
        'disponible_en_streaming',
    ];

    public function movieSessions()
    {
        return $this->hasMany(MovieSession::class);
    }
}
