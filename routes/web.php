<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ConfirmacionController;

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

// Rutas del carrito (públicas para ver, pero agregar requiere autenticación opcionalmente)
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::post('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth.session'])->group(function () {
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::get('/confirmar', [ConfirmacionController::class, 'index'])->name('confirmar');
    Route::post('/confirmar', [ConfirmacionController::class, 'confirmar'])->name('confirmar.post');
});

Route::fallback([ProductoController::class, 'notFound']);