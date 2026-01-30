<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Ruta raíz - Redirección según rol
Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

// Login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas por Rol
|--------------------------------------------------------------------------
*/

// Incluir rutas de administrador
require __DIR__.'/admin.php';

// Incluir rutas de solicitante
require __DIR__.'/solicitante.php';

// Incluir rutas de almacén
require __DIR__.'/almacen.php';

// Incluir rutas de Firebase (FCM)
require __DIR__.'/firebase.php';

/*
|--------------------------------------------------------------------------
| Rutas de Testing y Desarrollo
|--------------------------------------------------------------------------
*/

Route::get('/test/pdf', [TestController::class, 'testPdf'])->name('test.pdf');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/auth/check', [AuthController::class, 'check'])->name('api.auth.check');
});
