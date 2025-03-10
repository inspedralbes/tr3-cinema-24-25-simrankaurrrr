<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Butaca extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'butacas';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'fila',
        'columna',
        'is_vip'
    ];

    // Relación con la tabla 'movie_sessions'
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
