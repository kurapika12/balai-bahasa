<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

Route::get('/', function () { return redirect('/login'); });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Middleware agar hanya user login yang bisa akses
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pegawai Upload
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // Admin Actions
    Route::post('/activities', [DashboardController::class, 'storeActivity']);
    Route::put('/activities/{id}', [DashboardController::class, 'updateActivity'])->name('activities.update'); // Route Baru
    Route::delete('/activities/{id}', [DashboardController::class, 'destroyActivity'])->name('activities.destroy');

    // Admin User Management
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');

    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
});
