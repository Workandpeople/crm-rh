<?php

// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
        ], [
            'email.required' => 'Veuillez saisir votre e-mail.',
            'password.required' => 'Veuillez saisir votre mot de passe.',
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            // Redirection intelligente (ou selon rôle plus tard)
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Connexion réussie.');
        }

        return back()->withErrors(['email' => 'Identifiants invalides.'])
                     ->onlyInput('email');
    }
}
