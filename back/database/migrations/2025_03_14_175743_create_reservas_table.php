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
        // Crear la tabla 'reservas'
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->nullable()->constrained('compras')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que hace la reserva
            $table->foreignId('movie_session_id')->constrained('movie_sessions')->onDelete('cascade'); // Sesión de cine
            $table->json('butaca_ids'); // Almacenar el array de butaca_id como JSON
            $table->string('estado')->default('reservada'); // Solo un estado por defecto
            $table->decimal('precio', 8, 2); // Columna para el precio de la reserva
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Eliminar la tabla 'reservas'
        Schema::dropIfExists('reservas');
    }
};
