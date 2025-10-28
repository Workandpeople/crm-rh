@extends('layouts.landing')

@section('title', 'Connexion - CRM RH')

@section('content')
<div class="login min-vh-100 d-flex align-items-center justify-content-center bg-dark text-light">
    <div class="card shadow-lg border-0">

         <div class="card-body p-5">
            {{-- Logo + titre --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h2 class="fw-bold mt-3 text-light">Espace RH</h2>
                <p class="text-secondary small">Connexion sécurisée au CRM</p>
            </div>

            {{-- Messages d'erreur / succès --}}
            @if ($errors->any())
                <div class="alert alert-danger text-center py-2">Identifiants invalides.</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger text-center py-2">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success text-center py-2">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('login.attempt') }}" class="text-start">
                @csrf
                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label text-light fw-semibold">E-mail</label>
                    <input type="email" name="email" id="email"
                           class="form-control bg-dark text-light border-secondary"
                           placeholder="exemple@entreprise.com" required autofocus>
                </div>

                {{-- Mot de passe --}}
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label text-light fw-semibold">Mot de passe</label>
                    <input type="password" name="password" id="password"
                           class="form-control bg-dark text-light border-secondary pe-5"
                           placeholder="••••••••" required>
                           {{-- Icone de l'œil --}}
                    <span class="position-absolute end-0 translate-middle-y me-3 text-secondary"
                          style="cursor: pointer; top: 73%;" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </span>
                </div>

                {{-- Bouton se connecter--}}
                <button type="submit"
                        class="btn w-100 py-2 fw-semibold"
                        style="background-color: #4f46e5; border: none; color: white;">
                    Se connecter
                </button>
            </form>

            {{-- Mot de passe oublié --}}
            <div class="text-center mt-4">
                <a href="{{ url('/forgot-password') }}" class="text-decoration-none text-light small">
                    <i class="fa-solid fa-key me-1"></i> Mot de passe oublié ?
                </a>
            </div>

        </div>
    </div>
</div>

{{-- J'ai crée ce script pour afficher ou masquer le mot de passe 🙂 --}}
@push('js')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>
@endpush
@endsection
