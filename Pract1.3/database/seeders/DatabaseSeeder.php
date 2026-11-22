<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'profesor@gmail.com'],
            ['name' => 'Profesor Demo', 'password' => '123456789', 'role' => User::PROFESOR]
        );

        $alumnos = [
            ['Joshue', 'joshue@gmail.com', 2, 9.8],
            ['Daniel', 'daniel@gmail.com', 5, 7.4],
            ['Emiliano', 'emiliano@gmail.com', 0, 8.9],
            ['Zoe', 'zoe@gmail.com', 12, 6.2],
            ['Diego', 'diego@gmail.com', 1, 9.1],
            ['Milo', 'milo@gmail.com', 3, 7.9],
        ];

        foreach ($alumnos as [$nombre, $email, $faltas, $calificacion]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $nombre, 'password' => '123456789', 'role' => User::ALUMNO]
            );

            Alumno::firstOrCreate(
                ['user_id' => $user->id],
                ['nombre' => $nombre, 'faltas' => $faltas, 'calificacion' => $calificacion]
            );
        }
    }
}
