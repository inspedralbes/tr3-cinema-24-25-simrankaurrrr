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
        Schema::create('butacas', function (Blueprint $table) {
            $table->id(); // Debería ser un bigIncrements
            $table->string('fila');
            $table->integer('columna');
            $table->boolean('vip')->default(false);
            $table->decimal('precio', 8, 2)->default(6.00);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butacas');
    }
};
