<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {   //crea la tabla movimientos
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->constrained('partidas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('posicion'); // 0 a 8
            $table->char('simbolo', 1); // X u O
            $table->timestamps();
            $table->unique(['partida_id', 'posicion']); //una casilla = un solo movimiento
        });
    }

    public function down(): void
    {   //elimina tabla movimientos
        Schema::dropIfExists('movimientos');
    }
};
