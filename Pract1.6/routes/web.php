<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('registro', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');

Route::middleware(['auth', 'role:admin,jugador'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Pantalla temporal: en la parte 3 se reemplaza por el lobby
    Route::view('inicio', 'inicio')->name('inicio');
});