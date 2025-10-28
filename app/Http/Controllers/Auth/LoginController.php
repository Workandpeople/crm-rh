<?php

// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.login');
    }

    public function login(Request $request)
    {
        try {
            // --- Validation des champs ---
            $credentials = $request->validate([
                'email'    => ['required', 'email'],
                'password' => ['required', 'string', 'min:6'],
            ], [
                'email.required' => 'Veuillez saisir votre e-mail.',
                'password.required' => 'Veuillez saisir votre mot de passe.',
            ]);

            $remember = $request->boolean('remember');

            // --- Tentative d’authentification ---
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                $user = Auth::user();

                Log::info('Connexion réussie', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'role'    => $user->role?->name,
                ]);

                // ✅ Redirection vers le tableau de bord principal
                return redirect()->intended('/dashboard')
                ->with('success', 'Connexion réussie.');
            }

            // --- Identifiants invalides ---
            return back()
                ->withErrors(['email' => 'Identifiants invalides.'])
                ->onlyInput('email');

        } catch (\Throwable $e) {
            // --- Erreur inattendue ---
            Log::error('Erreur lors de la connexion', [
                'message' => $e->getMessage(),
                'email'   => $request->input('email'),
            ]);

            return back()
                ->with('error', 'Une erreur inattendue est survenue. Veuillez réessayer.')
                ->onlyInput('email');
        }
    }
}
