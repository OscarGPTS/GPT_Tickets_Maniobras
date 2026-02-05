<?php

use App\Http\Controllers\Api\MobileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rutas para la aplicación móvil del sistema de tickets.
| Sin middleware de autenticación - validación por email.
|
*/

// Rutas para versión móvil
Route::prefix('mobile')->group(function () {
    
    // Obtener tickets del usuario de almacén
    Route::post('/tickets', [MobileController::class, 'getTickets']);
    
    // Completar ticket
    Route::post('/tickets/complete', [MobileController::class, 'completeTicket']);
    
    // Obtener usuarios de almacén
    Route::get('/almacen-users', [MobileController::class, 'getAlmacenUsers']);
    
    // Asignar ticket a usuario de almacén
    Route::post('/tickets/assign', [MobileController::class, 'assignTicket']);
    
});
