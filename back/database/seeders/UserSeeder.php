<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuarios de prueba con datos completos
        User::create([
            'name' => 'Usuario 1',
            'email' => 'usuario1@example.com',
            'password' => bcrypt('password123'),
            'phone' => '123-456-7890',
            'address' => 'Calle Ficticia 123, Ciudad, País',
            'birthdate' => '1990-01-01',
            'role' => 'admin', // Puedes ajustar el rol según lo que desees (admin, user, etc.)
            'auth_token' => null, // El token será generado más tarde
        ]);
        
        User::create([
            'name' => 'Usuario 2',
            'email' => 'usuario2@example.com',
            'password' => bcrypt('password123'),
            'phone' => '234-567-8901',
            'address' => 'Avenida Real 456, Ciudad, País',
            'birthdate' => '1992-02-02',
            'role' => 'user',
            'auth_token' => null,
        ]);
        
        User::create([
            'name' => 'Usuario 3',
            'email' => 'usuario3@example.com',
            'password' => bcrypt('password123'),
            'phone' => '345-678-9012',
            'address' => 'Calle Luna 789, Ciudad, País',
            'birthdate' => '1988-03-03',
            'role' => 'user',
            'auth_token' => null,
        ]);
        
        User::create([
            'name' => 'Usuario 4',
            'email' => 'usuario4@example.com',
            'password' => bcrypt('password123'),
            'phone' => '456-789-0123',
            'address' => 'Calle Sol 101, Ciudad, País',
            'birthdate' => '1995-04-04',
            'role' => 'user',
            'auth_token' => null,
        ]);
        
        User::create([
            'name' => 'Usuario 5',
            'email' => 'usuario5@example.com',
            'password' => bcrypt('password123'),
            'phone' => '567-890-1234',
            'address' => 'Calle Estrella 202, Ciudad, País',
            'birthdate' => '1997-05-05',
            'role' => 'user',
            'auth_token' => null,
        ]);
    }
}
