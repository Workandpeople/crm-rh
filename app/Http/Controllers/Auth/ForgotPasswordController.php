<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequest()
    {
        return view('pages.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucun compte trouvé pour cette adresse.']);
        }

        // --- Génération du token ---
        $token = Str::random(60);

        // --- Sauvegarde dans la table password_reset_tokens ---
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // --- Construction du lien de réinitialisation ---
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email
        ], false));

        // --- Envoi du mail ---
        Mail::to($user->email)->send(new ResetPasswordMail($resetUrl, $user->first_name));

        return back()->with('success', 'Un e-mail de réinitialisation vous a été envoyé.');
    }
}
