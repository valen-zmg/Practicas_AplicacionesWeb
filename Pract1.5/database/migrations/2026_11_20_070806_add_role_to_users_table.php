
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo agregar role si todavía no existe
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('estudiante');
            });
        }
    }

    public function down(): void
    {
        // No eliminar role porque ya existía antes
        // de ejecutar esta migración.
    }
};

