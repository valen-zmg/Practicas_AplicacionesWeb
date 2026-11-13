<?php

use App\Http\Controllers\Practica1\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/practica1/login');

Route::prefix('practica1')->name('p1.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

    Route::get('verificar', [AuthController::class, 'mostrarVerificacion'])->middleware('signed')->name('verificar');
    Route::post('verificar-codigo', [AuthController::class, 'verificar'])->middleware('throttle:10,1')->name('verificar.post');

    Route::middleware(['auth', 'role:admin,empleado'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::view('/', 'practica1.inicio')->name('inicio');
    });
});