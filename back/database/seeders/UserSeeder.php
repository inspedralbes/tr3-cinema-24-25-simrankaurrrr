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
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('123456789'),
            'phone' => '123-456-7890',
            'address' => 'Calle Ficticia 123, Ciudad, País',
            'birthdate' => '1990-01-01',
            'role' => 'user', // Puedes ajustar el rol según lo que desees (admin, user, etc.)
            'auth_token' => null, // El token será generado más tarde
        ]);
        
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
            'phone' => '234-567-8901',
            'address' => 'Avenida Real 456, Ciudad, País',
            'birthdate' => '1992-02-02',
            'role' => 'admin',
            'auth_token' => null,
        ]);
        
    }
}
