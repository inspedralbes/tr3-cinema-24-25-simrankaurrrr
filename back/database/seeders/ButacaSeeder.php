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
        $filas = range('A', 'L'); 

        $columnas = range(1, 10); 

        foreach ($filas as $fila) {
            foreach ($columnas as $columna) {
                $isVip = ($fila === 'F') ? true : false;
                $precio = ($fila === 'F') ? 8.00 : 6.00; 

                // Crear la butaca
                Butaca::create([
                    'fila' => $fila,
                    'columna' => $columna,
                    'vip' => $isVip, 
                    'precio' => $precio, 
                ]);
            }
        }
    }
}
