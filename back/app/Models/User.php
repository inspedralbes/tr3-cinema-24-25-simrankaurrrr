<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'birthdate',
        'role',
        'auth_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'auth_token'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
    
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}