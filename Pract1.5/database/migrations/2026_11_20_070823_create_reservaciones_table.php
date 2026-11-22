<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->string('estado', 20)->default('activa'); // activa | cancelada
            $table->timestamp('cancelada_at')->nullable();
            $table->timestamps();
            $table->index(['equipo_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};
