<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\User;
use App\Models\MovieSession;
use App\Models\Entrada;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Asegúrate de que existe al menos un usuario
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Usuario de prueba',
                'email' => 'usuario@ejemplo.com',
                'password' => bcrypt('password')
            ]);
        }

        // Asegúrate de que existe al menos una sesión de película
        $movieSession = MovieSession::first();
        if (!$movieSession) {
            $movieSession = MovieSession::create([
                'movie_id' => 1, // Asegúrate de que exista una película con ID 1
                'session_time' => '16:00',
                'session_date' => '2025-03-06',
                'dia_espectador' => false
            ]);
        }

        // Asegúrate de que las entradas "Normal" y "VIP" existen
        $entradaNormal = Entrada::firstOrCreate(
            ['nombre_entrada' => 'Normal'],
            ['normal_precio' => 6.00, 'vip_precio' => 8.00, 'espectador_precio' => 4.00]
        );

        $entradaVIP = Entrada::firstOrCreate(
            ['nombre_entrada' => 'VIP'],
            ['normal_precio' => 6.00, 'vip_precio' => 8.00, 'espectador_precio' => 6.00]
        );

        // Crear las compras
        Compra::create([
            'user_id' => $user->id,
            'movie_session_id' => $movieSession->id,
            'ticket_quantity' => 2,
            'total_amount' => 12.00,  // 2 boletos normales
            'entry_type_id' => $entradaNormal->id,
        ]);

        Compra::create([
            'user_id' => $user->id,
            'movie_session_id' => $movieSession->id,
            'ticket_quantity' => 1,
            'total_amount' => 8.00,  // 1 boleto VIP
            'entry_type_id' => $entradaVIP->id,
        ]);
    }
}
