<?php

namespace Database\Seeders;

use App\Models\Entrada;
use Illuminate\Database\Seeder;

class EntradaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Entrada::create([
            'nombre_entrada' => 'Normal',
            'normal_precio' => 6.00,
            'vip_precio' => 8.00,
            'espectador_precio' => 4.00,
        ]);

        Entrada::create([
            'nombre_entrada' => 'VIP',
            'normal_precio' => 6.00,
            'vip_precio' => 8.00,
            'espectador_precio' => 6.00,
        ]);

        Entrada::create([
            'nombre_entrada' => 'Día del Espectador',
            'normal_precio' => 4.00,
            'vip_precio' => 6.00,
            'espectador_precio' => 4.00,
        ]);
    }
}
