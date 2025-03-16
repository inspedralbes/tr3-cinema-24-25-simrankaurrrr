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
        'vip',
        'precio' // Añadir el campo precio aquí
    ];

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
    // Relación con Compra (muchos a muchos)
    public function compras()
    {
        return $this->belongsToMany(Compra::class);
    }
    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
