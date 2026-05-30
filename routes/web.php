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
    Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/activities/{id}/download-all', [App\Http\Controllers\ReportController::class, 'downloadEventReports'])->name('activities.downloadAll');

    // Admin User Management
    Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');

    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::post('/reports/summarize', [App\Http\Controllers\ReportController::class, 'summarize'])->name('reports.summarize');

    // Rute-rute baru untuk Approval Workflow
    Route::patch('/reports/{id}/mark-reviewed', [App\Http\Controllers\ReportController::class, 'markAsReviewed'])->name('reports.markReviewed');
    Route::patch('/reports/{id}/update-status', [App\Http\Controllers\ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::post('/reports/{id}/revise', [App\Http\Controllers\ReportController::class, 'revise'])->name('reports.revise');
});
