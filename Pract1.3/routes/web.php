<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

Route::middleware(['auth', 'role:alumno,profesor'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('inicio', [AlumnoController::class, 'inicio'])->name('inicio');

    // Alumno: solo lectura de su propia información
    Route::get('mi-informacion', [AlumnoController::class, 'miInformacion'])
        ->middleware('role:alumno')->name('mi');

    // Profesor: registrar, modificar, eliminar y consultar. Un alumno recibe 403 aunque conozca la URL.
    Route::middleware('role:profesor')->group(function () {
        Route::resource('alumnos', AlumnoController::class)->except(['show']);
    });
});
