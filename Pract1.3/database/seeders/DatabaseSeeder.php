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
            ['email' => 'profesor@utt.test'],
            ['name' => 'Profesor Demo', 'password' => 'Password123!', 'role' => User::PROFESOR]
        );

        $alumnos = [
            ['Ana Martínez', 'ana@utt.test', 2, 9.8],
            ['Luis Hernández', 'luis@utt.test', 5, 7.4],
            ['María López', 'maria@utt.test', 0, 8.9],
            ['Carlos Ramírez', 'carlos@utt.test', 12, 6.2],
            ['Sofía Torres', 'sofia@utt.test', 1, 9.1],
        ];

        foreach ($alumnos as [$nombre, $email, $faltas, $calificacion]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $nombre, 'password' => 'Password123!', 'role' => User::ALUMNO]
            );

            Alumno::firstOrCreate(
                ['user_id' => $user->id],
                ['nombre' => $nombre, 'faltas' => $faltas, 'calificacion' => $calificacion]
            );
        }
    }
}
