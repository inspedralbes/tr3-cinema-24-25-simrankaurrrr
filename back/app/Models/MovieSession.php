<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSession extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'movie_sessions';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'movie_id',
        'session_time',
        'session_date',
        'dia_espectador'
    ];

    // Relación con la tabla 'movies'
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Relación con la tabla 'butacas'
    public function butacas()
    {
        return $this->hasMany(Butaca::class);
    }
}
