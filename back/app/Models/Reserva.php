<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // Aquí importamos el Model de Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory; // Si estás usando Factory

class Reserva extends Model
{
    protected $fillable = [
        'user_id', 'movie_session_id', 'butaca_id','precio', 'estado'
    ];

    public function butaca()
    {
        return $this->belongsTo(Butaca::class);
    }


    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }
}
