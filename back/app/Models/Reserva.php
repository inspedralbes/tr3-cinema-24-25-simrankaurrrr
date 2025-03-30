<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id', 'user_id', 'movie_session_id', 'butaca_ids', 'estado', 'precio'
    ];

    protected $casts = [
        'butaca_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class, 'movie_session_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
     public function butacas()
     {
         return $this->hasManyThrough(Butaca::class, ReservaButaca::class, 'reserva_id', 'id', 'id', 'butaca_id');
     }


public function butaca()
{
    return $this->belongsTo(Butaca::class);
}
}
