<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'compras';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id',
        'movie_session_id',
        'ticket_quantity',
        'total_amount',
        'entry_type_id' // Relación con el tipo de entrada
    ];

    // Relación con el modelo 'User'
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el modelo 'MovieSession'
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    // Relación con el modelo 'Entrada' (tipo de entrada)
    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entry_type_id');
    }
}
