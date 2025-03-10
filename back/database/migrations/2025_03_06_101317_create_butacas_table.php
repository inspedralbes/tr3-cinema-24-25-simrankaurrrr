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
            $table->id();
            $table->string('fila');
            $table->integer('columna');
            $table->boolean('vip')->default(false); // Agregamos el campo vip como booleano con valor por defecto 'false'
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
