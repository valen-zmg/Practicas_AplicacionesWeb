<?php

namespace Database\Seeders;

use App\Models\Equipo;
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

        $estudiantes = [
            'Joshue' => 'joshue@gmail.com',
            'Daniel' => 'daniel@gmail.com',
            'Emiliano' => 'emiliano@gmail.com',
            'Zoe' => 'zoe@gmail.com',
            'Diego' => 'diego@gmail.com',
            'Milo' => 'milo@gmail.com',
        ];

        foreach ($estudiantes as $nombre => $correo) {
            User::firstOrCreate(
                ['email' => $correo],
                ['name' => $nombre, 'password' => '123456789', 'role' => User::ESTUDIANTE]
            );
        }

        for ($i = 1; $i <= 8; $i++) {
            Equipo::firstOrCreate(
                ['codigo' => sprintf('PC-%02d', $i)],
                [
                    'nombre' => "Computadora laboratorio {$i}",
                    'descripcion' => 'Intel Core i5 · 16 GB RAM · SSD 512 GB',
                    'estado' => Equipo::DISPONIBLE,
                ]
            );
        }
    }
}
