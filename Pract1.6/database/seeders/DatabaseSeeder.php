<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Administrador', 'password' => 'Password123!', 'role' => User::ADMIN]
        );

        $jugadores = [
            'Joshue' => 'joshue@gmail.com',
            'Daniel' => 'daniel@gmail.com',
            'Emiliano' => 'emiliano@gmail.com',
            'Zoe' => 'zoe@gmail.com',
            'Kenneth' => 'kenneth@gmail.com',
            'Diego' => 'diego@gmail.com',
            'Milo' => 'milo@gmail.com',
        ];

        foreach ($jugadores as $nombre => $correo) {
            User::firstOrCreate(
                ['email' => $correo],
                ['name' => $nombre, 'password' => '123456789', 'role' => User::JUGADOR]
            );
        }
    }
}