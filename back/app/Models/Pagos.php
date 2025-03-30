<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'compra_id',
        'numero_tarjeta',
        'fecha_vencimiento',
        'cvv',
        'nombre', 
        'apellido', 
        'email',
    ];

    public $timestamps = true;
}

class Compra extends Model
{
    use HasFactory;

    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'compra_id');
    }
}
