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

            @if (session('success'))
                <div class="alert alert-success py-2 text-center">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <div class="mb-3 position-relative">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password"
                           class="form-control bg-dark text-light border-secondary pe-5" required>
                    <span class="position-absolute end-0 translate-middle-y me-3 text-secondary"
                          style="cursor:pointer; top: 68%;" onclick="togglePassword('password', 'eye1')">
                        <i class="fa-solid fa-eye" id="eye1"></i>
                    </span>
                </div>

                <div class="mb-4 position-relative">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control bg-dark text-light border-secondary pe-5" required>
                    <span class="position-absolute end-0 translate-middle-y me-3 text-secondary"
                          style="cursor:pointer; top: 68%;" onclick="togglePassword('password_confirmation', 'eye2')">
                        <i class="fa-solid fa-eye" id="eye2"></i>
                    </span>
                </div>

                <button class="btn w-100 fw-semibold" style="background-color:#4F46E5; color:#fff;">Valider</button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-decoration-none text-light small">
                    ← Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function togglePassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeId);
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
