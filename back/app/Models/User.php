<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Tabla asociada al modelo
    protected $table = 'users';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    // Relación con la tabla 'compras'
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
