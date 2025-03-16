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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('sinopsis');
            $table->string('duracion');
            $table->string('director');
            $table->string('actores');
            $table->integer('año');
            $table->string('genero');
            $table->string('poster_url');
            $table->string('trailer_url')->nullable();
            $table->string('idioma')->default('Español');
            $table->boolean('subtitulos')->default(true);
            $table->string('formato')->default('2D');
            $table->boolean('disponible_en_streaming')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
