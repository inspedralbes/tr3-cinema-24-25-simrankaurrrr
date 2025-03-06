<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'password' => bcrypt('password123'),
        ]);

        User::create([
            'name' => 'Ana Gómez',
            'email' => 'ana.gomez@example.com',
            'password' => bcrypt('password123'),
        ]);
    }
}
