<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('registro', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');

Route::middleware(['auth', 'role:admin,jugador'])->group(function () {
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('inicio', [PartidaController::class, 'inicio'])->name('inicio');

    //ver una partida: los participantes (y el administrador, en modo lectura)
    Route::get('partidas/{partida}', [PartidaController::class, 'show'])->whereNumber('partida')->name('partidas.show');

    //solo jugadores: un administrador recibe 403 aunque conozca la URL
    Route::middleware('role:jugador')->group(function () {
        Route::post('partidas', [PartidaController::class, 'crear'])->name('partidas.crear');
        Route::post('partidas/{partida}/unirse', [PartidaController::class, 'unirse'])->whereNumber('partida')->name('partidas.unirse');
        Route::post('partidas/{partida}/mover', [PartidaController::class, 'mover'])->whereNumber('partida')->name('partidas.mover');
        Route::post('partidas/{partida}/abandonar', [PartidaController::class, 'abandonar'])->whereNumber('partida')->name('partidas.abandonar');
        Route::get('historial', [PartidaController::class, 'historial'])->name('historial');
    });

    //solo admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('partidas', [AdminController::class, 'partidas'])->name('partidas.index');
    Route::get('historial', [AdminController::class, 'historial'])->name('historial');
    Route::post('partidas/{partida}/cancelar', [AdminController::class, 'cancelar'])->whereNumber('partida')->name('partidas.cancelar');
    Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });
});