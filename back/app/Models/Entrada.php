<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'entradas';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'nombre_entrada',
        'normal_precio',
        'vip_precio',
        'espectador_precio'
    ];

    // Relación con la tabla 'compras'
    public function compras()
    {
        return $this->hasMany(Compra::class, 'entry_type_id');
    }
}
