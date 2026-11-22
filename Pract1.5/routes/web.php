<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ReservacionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

Route::middleware(['auth', 'role:admin,estudiante'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('equipos', [EquipoController::class, 'index'])->name('equipos.index');

    // Detalle y cancelación: el controlador comprueba que sea el dueño (o admin)
    Route::get('reservaciones/{reservacion}', [ReservacionController::class, 'show'])
        ->whereNumber('reservacion')->name('reservaciones.show');
    Route::post('reservaciones/{reservacion}/cancelar', [ReservacionController::class, 'cancelar'])
        ->whereNumber('reservacion')->name('reservaciones.cancelar');

    // Solo estudiante
    Route::middleware('role:estudiante')->group(function () {
        Route::post('equipos/{equipo}/reservar', [ReservacionController::class, 'reservar'])->name('equipos.reservar');
        Route::get('mis-reservaciones', [ReservacionController::class, 'mias'])->name('reservaciones.mias');
    });

    // Solo administrador
    Route::middleware('role:admin')->group(function () {
        Route::resource('equipos', EquipoController::class)->except(['index', 'show']);
        Route::get('reservaciones', [ReservacionController::class, 'todas'])->name('reservaciones.todas');
    });
});
