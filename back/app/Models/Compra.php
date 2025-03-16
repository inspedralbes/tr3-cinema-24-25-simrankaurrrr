<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'user_id',
        'movie_session_id',
        'butaca_id',  // Añadido el campo butaca_id
        'ticket_quantity',
        'total_amount',
        'estado'  // Agregar el campo 'estado' para poder actualizarlo
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con MovieSession
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

// Relación con Butacas (si hay múltiples butacas en una compra)
public function butacas()
{
    return $this->hasMany(Butaca::class);
}


    // Relación con Pagos
    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'compra_id');
    }

    // Relación con Reserva
    public function reserva()
    {
        return $this->hasOne(Reserva::class, 'compra_id');
    }
}
