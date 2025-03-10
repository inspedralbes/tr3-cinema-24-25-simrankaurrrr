<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Butaca;

class ButacaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Filas de A a L
        $filas = range('A', 'L'); 

        // Columnas de 1 a 10
        $columnas = range(1, 10); 

        // Iterar sobre cada fila y columna
        foreach ($filas as $fila) {
            foreach ($columnas as $columna) {
                // Si la fila es la fila 6 (F), asignar VIP
                $isVip = ($fila === 'F') ? true : false;

                // Crear la butaca
                Butaca::create([
                    'fila' => $fila,
                    'columna' => $columna,
                    'vip' => $isVip, // Asignamos el valor de VIP
                ]);
            }
        }
    }
}
