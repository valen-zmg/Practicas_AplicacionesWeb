<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('existencias')->default(0);
            $table->string('estado', 15)->default('disponible'); // disponible | agotado
            $table->timestamps();                                 // created_at = fecha de registro
            $table->index(['categoria_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
