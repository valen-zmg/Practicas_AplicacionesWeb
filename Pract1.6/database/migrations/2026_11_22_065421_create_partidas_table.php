<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_x_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jugador_o_id')->nullable()->constrained('users')->nullOnDelete();
            $table->char('tablero', 9)->default('---------');       // '-' = casilla libre
            $table->char('turno', 1)->default('X');                  // a quién le toca
            $table->string('estado', 20)->default('esperando');      // esperando | en_curso | terminada | cancelada
            $table->string('resultado', 20)->nullable();             // victoria | empate | abandono | cancelada
            $table->foreignId('ganador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
