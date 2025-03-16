<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'user_id',
        'movie_session_id',
        'butaca_ids',
        'ticket_quantity',
        'total_amount',
        'estado'
    ];

    // Indicamos que el campo butaca_ids es de tipo JSON y debe ser tratado como un array
    protected $casts = [
        'butaca_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'compra_id');
    }

    public function reserva()
    {
        return $this->hasOne(Reserva::class, 'compra_id');
    }
}
