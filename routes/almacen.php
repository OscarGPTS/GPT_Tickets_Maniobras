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
            ->middleware('role:almacen,admin')
            ->name('dashboard');
        
        // Tickets
        Route::get('/tickets/pending', [TicketController::class, 'pending'])
            ->middleware('role:almacen,admin')
            ->name('tickets.pending');
        Route::get('/tickets/mine', [TicketController::class, 'mine'])
            ->middleware('role:almacen,admin')
            ->name('tickets.mine');
        Route::get('/tickets/export', [TicketController::class, 'export'])
            ->middleware('role:almacen,admin')
            ->name('tickets.export');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
            ->middleware('role:almacen,admin')
            ->name('tickets.show');
        
        // Asignación y gestión de tickets
        Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
            ->middleware('role:almacen,admin')
            ->name('tickets.assign');
        Route::post('/tickets/{ticket}/progress', [TicketController::class, 'addProgress'])
            ->middleware('role:almacen,admin')
            ->name('tickets.progress');
        Route::post('/tickets/{ticket}/complete', [TicketController::class, 'complete'])
            ->middleware('role:almacen,admin')
            ->name('tickets.complete');
        
        // Notificaciones
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->middleware('role:almacen,admin')
            ->name('notifications.index');
        Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
            ->middleware('role:almacen,admin')
            ->name('notifications.show');
        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->middleware('role:almacen,admin')
            ->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
            ->middleware('role:almacen,admin')
            ->name('notifications.mark-all-read');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
            ->middleware('role:almacen,admin')
            ->name('notifications.destroy');
        Route::delete('/notifications-read/delete-all', [NotificationController::class, 'deleteAllRead'])
            ->middleware('role:almacen,admin')
            ->name('notifications.delete-all-read');
    });
