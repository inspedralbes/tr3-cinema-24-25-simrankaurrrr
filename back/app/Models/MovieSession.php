<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieSession extends Model
{
    use HasFactory;

    protected $table = 'movie_sessions';
    protected $fillable = [
        'movie_id',
        'session_time',
        'session_date',
        'dia_espectador'
    ];
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function butacas()
    {
        return $this->hasMany(Butaca::class);
    }
}
