<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@utt.test')],
            ['name' => 'Administrador', 'password' => 'Password123!', 'role' => User::ADMIN]
        );

        User::firstOrCreate(
            ['email' => 'empleado@utt.test'],
            ['name' => 'Empleado Demo', 'password' => 'Password123!', 'role' => User::EMPLEADO]
        );
    }
}
