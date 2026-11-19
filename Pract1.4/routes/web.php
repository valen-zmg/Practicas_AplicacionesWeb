<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

Route::middleware(['auth', 'role:admin,empleado'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // admin y empleado: consultar, buscar, filtrar y ordenar
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');

    // solo admin: registrar, actualizar y eliminar. Un empleado recibe 403 aunque conozca la URL.
    Route::middleware('role:admin')->group(function () {
        Route::resource('productos', ProductoController::class)->except(['index', 'show']);
    });
});