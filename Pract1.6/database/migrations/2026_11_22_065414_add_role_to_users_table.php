<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration //adapta la tabla `users` para el gatiko
{
    public function up(): void //columnas `role` y `deleted_at` (SoftDeletes) a la tabla `users`
    {
        Schema::table('users', function (Blueprint $table) {
            //define el rol del usuario
            $table->string('role', 20)->default('jugador')->after('password');
            $table->softDeletes();
        });
    }

    public function down(): void
    {   //revierte los cambios
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropSoftDeletes();
        });
    }
};
