<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del Administrador
|--------------------------------------------------------------------------
|
| Rutas específicas para usuarios con rol de administrador.
| Incluye middleware de autenticación y verificación de rol admin.
|
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');
        
        // Gestión de usuarios
        Route::get('/users', [AdminController::class, 'users'])
            ->name('users.index');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])
            ->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])
            ->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])
            ->name('users.delete');
        
        // Asignación de roles (AJAX)
        Route::post('/users/{user}/assign-role', [AdminController::class, 'assignRole'])
            ->name('users.assign-role');
        
        // Estadísticas avanzadas
        Route::get('/statistics', [AdminController::class, 'statistics'])
            ->name('statistics');
        
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
