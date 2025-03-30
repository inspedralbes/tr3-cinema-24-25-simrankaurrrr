<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Butaca extends Model
{
    use HasFactory;

    protected $table = 'butacas';

    protected $fillable = [
        'fila',
        'columna',
        'vip',
        'precio' 
    ];

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
    public function compras()
    {
        return $this->belongsToMany(Compra::class);
    }
    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
