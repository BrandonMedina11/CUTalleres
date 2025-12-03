<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\InscripcionController;

// Rutas públicas
Route::get('/', [ProductoController::class, 'home'])->name('home');
Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('catalogo');
Route::get('/producto/{id}', [ProductoController::class, 'detalle'])->name('producto.detalle');

// Rutas de autenticación
Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth.session'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::get('/mis-pedidos', [PedidosController::class, 'misPedidos'])->name('mis-pedidos');
    Route::post('/inscripcion/{tallerId}', [InscripcionController::class, 'inscribir'])->name('inscripcion.inscribir');
});

Route::fallback([ProductoController::class, 'notFound']);