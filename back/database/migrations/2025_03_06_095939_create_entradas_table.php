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
        // Crear tabla de entradas (tipos de entradas y precios)
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();  // Identificador único del tipo de entrada
            $table->string('nombre_entrada');   // Nombre del tipo de entrada (Normal, VIP, Día del espectador)
            $table->decimal('normal_precio', 8, 2);  // Precio de la entrada normal
            $table->decimal('vip_precio', 8, 2);     // Precio de la entrada VIP (solo fila 6)
            $table->decimal('espectador_precio', 8, 2); // Precio de la entrada para el Día del espectador
            $table->timestamps();           // Tiempos de creación y actualización
        });

        // Relacionar la tabla de 'compras' con la tabla de 'entradas'
        // Añadir un campo 'entry_type_id' en 'compras' para asociar el tipo de entrada
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('entry_type_id')->constrained('entradas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la columna 'entry_type_id' de la tabla 'compras'
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['entry_type_id']);
            $table->dropColumn('entry_type_id');
        });

        // Eliminar la tabla 'entradas'
        Schema::dropIfExists('entradas');
    }
};
