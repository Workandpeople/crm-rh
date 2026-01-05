<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Active les vues Bootstrap pour les liens de pagination
        Paginator::useBootstrapFive();

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Définition du Gate pour la visualisation des backlogs
        Gate::define('view-backlogs', function ($user) {
            return in_array($user->role->name, ['superadmin', 'admin', 'chef_equipe', 'employe']);
        });
    }
}
