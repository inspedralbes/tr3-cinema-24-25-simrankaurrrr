<?php
// Modelo Reserva
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'compras_id', 'user_id', 'movie_session_id', 'butaca_id', 'estado', 'precio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class, 'movie_session_id');
    }

    public function butaca()
    {
        return $this->belongsTo(Butaca::class, 'butaca_id');
    }

    // Relación con Compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
}
