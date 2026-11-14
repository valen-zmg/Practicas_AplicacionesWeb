<?php

use App\Http\Controllers\Practica1\AuthController;
use App\Http\Controllers\Practica1\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/practica1/login');

Route::prefix('practica1')->name('p1.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

    // Etapas 2 y 3 (solo admin). La ruta GET es temporal y firmada.
    Route::get('verificar', [AuthController::class, 'mostrarVerificacion'])->middleware('signed')->name('verificar');
    Route::post('verificar-codigo', [AuthController::class, 'verificar'])->middleware('throttle:10,1')->name('verificar.post');

    Route::middleware(['auth', 'role:admin,empleado'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('p1.usuarios.index'))->name('inicio');

        // Admin y empleado: registrar, listar, ver detalle y editar
        Route::resource('usuarios', UsuarioController::class)->except(['destroy']);

        // SOLO admin: aunque un empleado conozca la URL, recibe 403
        Route::middleware('role:admin')->group(function () {
            Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
            Route::post('usuarios/{id}/restaurar', [UsuarioController::class, 'restore'])->name('usuarios.restore');
        });
    });
});