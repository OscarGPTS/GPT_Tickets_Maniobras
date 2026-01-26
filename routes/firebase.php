<?php

use App\Http\Controllers\FirebaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Firebase Cloud Messaging - API de Prueba
|--------------------------------------------------------------------------
| 
| API de prueba para enviar notificaciones FCM.
| Para uso en producción, utiliza FCMService directamente en tu código.
|
*/

// API de Prueba (sin autenticación)
Route::get('/api/test-notification/{userId}', [FirebaseController::class, 'testNotification'])
    ->name('fcm.test');
