<?php

use App\Http\Controllers\Solicitante\DashboardController;
use App\Http\Controllers\Solicitante\TicketController;
use App\Http\Controllers\Solicitante\SurveyController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Solicitante
|--------------------------------------------------------------------------
|
| Rutas específicas para usuarios con rol de solicitante.
| Incluye middleware de autenticación y verificación de rol.
|
*/

Route::middleware(['auth', 'role:solicitante'])
    ->prefix('solicitante')
    ->name('solicitante.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Tickets
        Route::get('/tickets', [TicketController::class, 'index'])
            ->name('tickets.index');
        Route::get('/tickets/create', [TicketController::class, 'create'])
            ->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])
            ->name('tickets.store');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->name('tickets.show');
        Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])
            ->name('tickets.edit');
        Route::put('/tickets/{ticket}', [TicketController::class, 'update'])
            ->name('tickets.update');
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])
            ->name('tickets.destroy');
        Route::delete('/tickets/{ticket}/images/{image}', [TicketController::class, 'deleteImage'])
            ->name('tickets.images.delete');
        Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])
            ->name('tickets.cancel');
        
        // Encuestas
        Route::get('/surveys', [SurveyController::class, 'index'])
            ->withoutMiddleware('role:solicitante')
            ->middleware('role:solicitante,admin')
            ->name('surveys.index');
        Route::get('/surveys/pending', [SurveyController::class, 'pending'])
            ->name('surveys.pending');
        Route::get('/surveys/{survey}', [SurveyController::class, 'show'])
            ->withoutMiddleware('role:solicitante')
            ->middleware('role:solicitante,admin')
            ->name('surveys.show');
        Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])
            ->name('surveys.edit');
        Route::put('/surveys/{survey}', [SurveyController::class, 'update'])
            ->name('surveys.update');
        Route::post('/surveys/{survey}/complete', [SurveyController::class, 'complete'])
            ->name('surveys.complete');
        Route::post('/surveys/{survey}/quick-complete', [SurveyController::class, 'quickComplete'])
            ->name('surveys.quickComplete');
        Route::post('/surveys/quick-complete-all', [SurveyController::class, 'quickCompleteAll'])
            ->name('surveys.quickCompleteAll');
        
        // Notificaciones
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
            ->name('notifications.show');
        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->name('notifications.destroy');
        Route::delete('/notifications-read/delete-all', [NotificationController::class, 'deleteAllRead'])
            ->name('notifications.delete-all-read');
    });
