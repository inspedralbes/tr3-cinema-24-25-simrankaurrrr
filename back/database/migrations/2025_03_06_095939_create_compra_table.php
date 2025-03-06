<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      

        // Crear la tabla 'compras'
        Schema::create('compras', function (Blueprint $table) {
            $table->id();  // ID principal de la compra
            $table->foreignId('user_id')->constrained('users');  // Clave foránea a la tabla 'users'
            $table->foreignId('movie_session_id')->constrained('movie_sessions');  // Clave foránea a la tabla 'movie_sessions'
            $table->integer('ticket_quantity');  // Cantidad de boletos comprados
            $table->decimal('total_amount', 8, 2);  // Monto total de la compra
            $table->timestamps();  // Tiempos de creación y actualización
        });

        // Habilitar las comprobaciones de claves foráneas nuevamente
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la tabla 'compras'
        Schema::dropIfExists('compras');
    }
};
