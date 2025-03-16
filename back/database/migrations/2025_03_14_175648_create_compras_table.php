<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->json('butaca_ids');  // Almacenar el array de butaca_id como JSON
            $table->integer('ticket_quantity');  // Cantidad de boletos comprados
            $table->decimal('total_amount', 8, 2);  // Monto total de la compra
            $table->string('estado')->default('en_proceso');  // Estado de la compra, con valor por defecto 'en_proceso'
            $table->timestamps();  // Tiempos de creación y actualización
        });
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
