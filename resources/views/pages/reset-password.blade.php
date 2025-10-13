@extends('layouts.landing')
@section('title', 'Réinitialiser le mot de passe - CRM RH')

@section('content')
<div class="reset-password min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0">
        <div class="card-body p-5 text-light">

            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px;">
                <h2 class="fw-bold mt-3">Nouveau mot de passe</h2>
                <p class="text-secondary small">Saisissez et confirmez votre nouveau mot de passe.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2">Vérifiez les informations saisies.</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="password" class="form-control bg-dark text-light border-secondary" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control bg-dark text-light border-secondary" required>
                </div>

                <button class="btn w-100 fw-semibold" style="background-color:#4F46E5; color:#fff;">Valider</button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login.show') }}" class="text-decoration-none text-light small">
                    ← Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
