<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuarios de prueba
        User::create([
            'name' => 'Usuario 1',
            'email' => 'usuario1@example.com',
            'password' => bcrypt('password123'),
        ]);
        
        User::create([
            'name' => 'Usuario 2',
            'email' => 'usuario2@example.com',
            'password' => bcrypt('password123'),
        ]);
        
        User::create([
            'name' => 'Usuario 3',
            'email' => 'usuario3@example.com',
            'password' => bcrypt('password123'),
        ]);
        
        User::create([
            'name' => 'Usuario 4',
            'email' => 'usuario4@example.com',
            'password' => bcrypt('password123'),
        ]);
        
        User::create([
            'name' => 'Usuario 5',
            'email' => 'usuario5@example.com',
            'password' => bcrypt('password123'),
        ]);
    }
}
