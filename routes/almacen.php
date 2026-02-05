<?php

use App\Http\Controllers\Almacen\DashboardController;
use App\Http\Controllers\Almacen\TicketController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Personal de Almacén
|--------------------------------------------------------------------------
|
| Rutas específicas para usuarios con rol de almacén.
| Incluye middleware de autenticación y verificación de rol.
| Admin tiene acceso a todas las rutas de almacén.
|
*/

Route::middleware(['auth'])
    ->prefix('almacen')
    ->name('almacen.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        
        // Tickets
        Route::get('/tickets/pending', [TicketController::class, 'pending'])
            ->name('tickets.pending');
        Route::get('/tickets/mine', [TicketController::class, 'mine'])
            ->name('tickets.mine');
        Route::get('/tickets/history', [TicketController::class, 'history'])
            ->name('tickets.history');
        Route::get('/tickets/export', [TicketController::class, 'export'])
            ->name('tickets.export');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->name('tickets.show');
            
        
        // Asignación y gestión de tickets
        Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
            ->name('tickets.assign');
        Route::post('/tickets/{ticket}/reject', [TicketController::class, 'reject'])
            ->name('tickets.reject');
        Route::post('/tickets/{ticket}/progress', [TicketController::class, 'addProgress'])
            ->name('tickets.progress');
        Route::post('/tickets/{ticket}/complete', [TicketController::class, 'complete'])
            ->name('tickets.complete');
        
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
