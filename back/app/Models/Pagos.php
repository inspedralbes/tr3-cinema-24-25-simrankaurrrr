<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Ejemplo de modelo Pagos:
class Pagos extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'compra_id',
        'numero_tarjeta',
        'fecha_vencimiento',
        'cvv',
    ];

    public $timestamps = true;
}

// Relación en el modelo Compra:
class Compra extends Model
{
    use HasFactory;

    // Relación con Pagos
    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'compra_id');
    }
}
