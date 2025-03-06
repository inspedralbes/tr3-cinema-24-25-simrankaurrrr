<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'movies';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'title',
        'sinopsis',
        'duracion',
        'director',
        'actores',
        'año',
        'genero',
        'poster_url'
    ];

    // Relación con la tabla 'movie_sessions'
    public function movieSessions()
    {
        return $this->hasMany(MovieSession::class);
    }
}
