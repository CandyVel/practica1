<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; // <--- Verifica que esta línea esté aquí arriba

// 1. Pantalla de inicio (Login / Registro)
Route::get('/', function () {
    return view('index');
});

// 2. Cerebro del Login
Route::post('/login', [AuthController::class, 'login'])->name('login');

// 3. Cerebro del Registro (Esta es la que hace que el formulario naranja funcione)
Route::post('/registro', [UserController::class, 'store']);

// 4. Pantallas de la App (Vistas)
Route::get('/app', function () { return view('app'); });
Route::get('/rutas', function () { return view('rutas'); });
Route::get('/actividades', function () { return view('actividades'); });
Route::get('/entrenamiento', function () { return view('entrenamiento'); });

// 5. RUTAS DEL CRUD (Solo para el Administrador)
Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios/crear', [UserController::class, 'store'])->name('usuarios.store');
Route::put('/usuarios/editar/{id}', [UserController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/borrar/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');