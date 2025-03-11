<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_session_id',
        'butaca_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movieSession()
    {
        return $this->belongsTo(MovieSession::class);
    }
    

// app/Models/Reserva.php
public function butaca()
{
    return $this->hasOne(Butaca::class);
}

}
