<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }

    public function loadPage($page)
    {
        $view = "components.sidebarContent.{$page}";
        if (View::exists($view)) {
            return view($view);
        }

        return response("<p class='p-3 text-warning'>⚠️ Vue introuvable : {$page}</p>", 404);
    }
}
