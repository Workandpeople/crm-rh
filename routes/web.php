<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\View;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequest'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')->name('logout');

// Exemples de routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => view('pages.dashboard'))->name('dashboard');

    // Navigation dynamique déjà en place (protégée)
    Route::get('/dashboard/{page}', function ($page) {
        $view = 'components.sidebarContent.' . $page;
        return \Illuminate\Support\Facades\View::exists($view)
            ? view($view)
            : response("<div class='p-4 text-warning'>Vue introuvable : {$page}</div>", 404);
    });
});

Route::middleware(['auth','role:superadmin'])->group(function () {
    Route::get('/superadmin', function () {
        return 'Section superadmin';
    });
});
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Section admin';
    });
});
Route::middleware(['auth','role:user'])->group(function () {
    Route::get('/user', function () {
        return 'Section utilisateur';
    });
});
