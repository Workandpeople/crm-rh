<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::get('/', function () {
    return view('pages.login'); // ou le nom de ta page
});

Route::get('/forgot-password', function () {
    return view('pages.forgot-password');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

// Route pour charger les contenus dynamiques
Route::get('/dashboard/{page}', function ($page) {
    $viewPath = 'components.sidebarContent.' . $page;

    if (View::exists($viewPath)) {
        return view($viewPath);
    } else {
        return response("<div class='p-4 text-danger'>⚠️ Vue introuvable : {$page}</div>", 404);
    }
});
