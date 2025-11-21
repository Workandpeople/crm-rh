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
        // 🔹 D'abord les routes fixes
        Route::get('/users/options', [\App\Http\Controllers\Admin\UserController::class, 'options'])
            ->name('admin.users.options');

        // 🔹 Ensuite les CRUD dynamiques
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('admin.users.index');

        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])
            ->name('admin.users.show');

        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])
            ->name('admin.users.store');

        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('admin.users.update');

        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
            ->name('admin.users.reset');

        // === Companies ===
        Route::get('/companies/options', [\App\Http\Controllers\Admin\CompanyController::class, 'options'])
            ->name('admin.companies.options');

        Route::get('/companies', [\App\Http\Controllers\Admin\CompanyController::class, 'index'])
            ->name('admin.companies.index');

        Route::get('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'show'])
            ->name('admin.companies.show');

        Route::post('/companies', [\App\Http\Controllers\Admin\CompanyController::class, 'store'])
            ->name('admin.companies.store');

        Route::put('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'update'])
            ->name('admin.companies.update');

        Route::delete('/companies/{company}', [\App\Http\Controllers\Admin\CompanyController::class, 'destroy'])
            ->name('admin.companies.destroy');

        // === Teams ===
        Route::get('/teams/options', [\App\Http\Controllers\Admin\TeamController::class, 'options'])
            ->name('admin.teams.options');

        Route::get('/teams', [\App\Http\Controllers\Admin\TeamController::class, 'index'])
            ->name('admin.teams.index');

        Route::get('/teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'show'])
            ->name('admin.teams.show');

        Route::post('/teams', [\App\Http\Controllers\Admin\TeamController::class, 'store'])
            ->name('admin.teams.store');

        Route::put('/teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'update'])
            ->name('admin.teams.update');

        Route::delete('/teams/{team}', [\App\Http\Controllers\Admin\TeamController::class, 'destroy'])
            ->name('admin.teams.destroy');

        // === Backlogs ===
        Route::get('/backlogs', [\App\Http\Controllers\Admin\BacklogController::class, 'index'])
        ->middleware('can:view-backlogs')
        ->name('admin.backlogs.index');

        // Options (assignés possibles)
        Route::get('/backlogs/options', [\App\Http\Controllers\Admin\BacklogController::class, 'options'])
            ->middleware('can:view-backlogs')
            ->name('admin.backlogs.options');

        // Création d’un ticket
        Route::post('/backlogs', [\App\Http\Controllers\Admin\BacklogController::class, 'store'])
            ->middleware('can:view-backlogs') // a modifier en fonction des permissions plus tard
            ->name('admin.backlogs.store');

        // Changement de statut
        Route::patch('/backlogs/{ticket}/status', [\App\Http\Controllers\Admin\BacklogController::class, 'updateStatus'])
            ->middleware('can:view-backlogs') // idem ici, à affiner après
            ->name('admin.backlogs.status');

        Route::get('/backlogs/{ticket}', [\App\Http\Controllers\Admin\BacklogController::class, 'show'])
            ->middleware('can:view-backlogs')
            ->name('admin.backlogs.show');

        // === Congés / Absences (admin) ===
        Route::get('/leaves', [\App\Http\Controllers\Admin\LeaveController::class, 'index'])
            ->middleware('can:view-backlogs') // ou un autre gate si tu préfères
            ->name('admin.leaves.index');

        Route::get('/leaves/{leave}', [\App\Http\Controllers\Admin\LeaveController::class, 'show'])
            ->name('admin.leaves.show');

        Route::patch('/leaves/{leave}/status', [\App\Http\Controllers\Admin\LeaveController::class, 'updateStatus'])
            ->name('admin.leaves.status');
    });
});
