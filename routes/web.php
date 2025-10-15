<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Rutas de Google OAuth directo
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Notificaciones
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [DashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [DashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.readAll');
    
    // Tickets - Rutas para usuarios normales
    Route::resource('tickets', TicketController::class);
    Route::delete('/tickets/{ticket}/images/{image}', [TicketController::class, 'deleteImage'])->name('tickets.images.delete');
    
    // Encuestas
    Route::resource('surveys', SurveyController::class)->except(['create', 'store', 'destroy']);
    Route::get('/surveys/pending', [SurveyController::class, 'pending'])->name('surveys.pending');
    Route::post('/surveys/{survey}/complete', [SurveyController::class, 'completeSimple'])->name('surveys.complete');
    Route::post('/surveys/{survey}/quick-complete', [SurveyController::class, 'quickComplete'])->name('surveys.quickComplete');
    Route::post('/surveys/quick-complete-all', [SurveyController::class, 'quickCompleteAll'])->name('surveys.quickCompleteAll');

    
});

Route::get('/test/pdf', [TestController::class, 'testPdf'])->name('test.pdf');

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Almacén
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:almacen'])->prefix('almacen')->name('almacen.')->group(function () {
    
    // Dashboard del almacén
    Route::get('/dashboard', [AlmacenController::class, 'dashboard'])->name('dashboard');
    
    // Tickets
    Route::get('/tickets/pending', [AlmacenController::class, 'pendingTickets'])->name('tickets.pending');
    Route::get('/tickets/my-tickets', [AlmacenController::class, 'myTickets'])->name('tickets.mine');
    Route::get('/tickets/{ticket}', [AlmacenController::class, 'showTicket'])->name('tickets.show');
    
    // Asignación y completado de tickets
    Route::post('/tickets/{ticket}/assign', [AlmacenController::class, 'assignTicket'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/progress', [AlmacenController::class, 'addProgress'])->name('tickets.progress');
    Route::post('/tickets/{ticket}/complete', [AlmacenController::class, 'completeTicket'])->name('tickets.complete');
    
    // Estadísticas
    Route::get('/statistics', [AlmacenController::class, 'statistics'])->name('statistics');
    
});

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Administración
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard de administración
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de usuarios
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Asignación de roles (AJAX)
    Route::post('/users/{user}/assign-role', [AdminController::class, 'assignRole'])->name('users.assign-role');
    
    // Estadísticas avanzadas
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    
});

/*
|--------------------------------------------------------------------------
| API Routes (si se necesitan)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/auth/check', [AuthController::class, 'check'])->name('api.auth.check');
});
