<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard'); // ta vue principale
    }

    public function loadPage(string $page)
    {
        try {
            // Cherche d'abord dans chaque sous-dossier par rôle
            $roles = ['superadmin', 'admin', 'employe'];
            foreach ($roles as $role) {
                $viewPath = "components.sidebarContent.{$role}.{$page}";
                if (View::exists($viewPath)) {
                    Log::info("✅ Chargement page {$viewPath}");
                    return view($viewPath);
                }
            }

            // Sinon fallback à la racine sidebarContent
            $fallbackPath = "components.sidebarContent.{$page}";
            if (View::exists($fallbackPath)) {
                Log::info("✅ Chargement page {$fallbackPath}");
                return view($fallbackPath);
            }

            Log::warning("⚠️ Vue introuvable : {$page}");
            return response("<p class='p-3 text-warning'>Page '{$page}' introuvable.</p>", 404);

        } catch (\Throwable $e) {
            Log::error("❌ Erreur page {$page} : ".$e->getMessage());
            return response("<p class='p-3 text-danger'>Erreur interne : {$e->getMessage()}</p>", 500);
        }
    }
}
