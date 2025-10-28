<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Auth\{
    LoginController,
    LogoutController,
    ForgotPasswordController,
    ResetPasswordController
};
use App\Http\Controllers\DashboardController;

// ======================================
//  AUTH INVITÉS (non connectés)
// ======================================
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLogin'])->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.attempt');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequest'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// ======================================
//  DÉCONNEXION
// ======================================
Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ======================================
//  ZONE AUTHENTIFIÉE
// ======================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Chargement AJAX des sous-pages
    Route::get('/dashboard/{page}', [DashboardController::class, 'loadPage'])
        ->where('page', '^[A-Za-z0-9_-]+$')
        ->name('dashboard.page');

    // === Super Admin actions ===
    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    });
});
