<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade'); // Relación con la tabla 'compras', se elimina si la compra se elimina
            $table->string('numero_tarjeta');  // Número de tarjeta
            $table->string('fecha_vencimiento');  // Fecha de vencimiento de la tarjeta
            $table->string('cvv');  // Código CVV
            $table->string('nombre');  // Nombre del titular de la tarjeta
            $table->string('apellido');  // Apellido del titular de la tarjeta
            $table->string('email');  // Email del titular de la tarjeta
            $table->timestamps();  // Tiempos de creación y actualización
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
