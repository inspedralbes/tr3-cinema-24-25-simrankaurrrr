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
        'movie_session_id',
        'fila',
        'columna',
        'ocupada',
        'is_vip'
    ];

    // Relación con la tabla 'movie_sessions'
    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }
}
