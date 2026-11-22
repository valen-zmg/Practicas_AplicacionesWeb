<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            ['name' => 'Administrador', 'password' => 'Password123!', 'role' => User::ADMIN]
        );

        User::firstOrCreate(
            ['email' => 'empleado@gmail.com'],
            ['name' => 'Empleado', 'password' => '123456789', 'role' => User::EMPLEADO]
        );
    }
}
