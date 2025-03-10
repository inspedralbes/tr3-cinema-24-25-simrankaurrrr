<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que hace la reserva
            $table->foreignId('movie_session_id')->constrained('movie_sessions')->onDelete('cascade'); // Sesión de cine
            $table->foreignId('butaca_id')->constrained('butacas')->onDelete('cascade'); // Butaca reservada
            $table->string('estado')->default('reservada'); // Columna de estado, con valor por defecto 'reservada'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
