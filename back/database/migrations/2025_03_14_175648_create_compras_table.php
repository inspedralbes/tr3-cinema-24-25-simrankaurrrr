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
            $table->id();  
            $table->foreignId('user_id')->constrained('users');  
            $table->foreignId('movie_session_id')->constrained('movie_sessions');  
            $table->json('butaca_ids');  
            $table->integer('ticket_quantity'); 
            $table->decimal('total_amount', 8, 2);
            $table->string('estado')->default('en_proceso');  
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
};
