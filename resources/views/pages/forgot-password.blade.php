@extends('layouts.landing')

@section('title', 'Mot de passe oublié - CRM RH')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center text-light"
     style="background: linear-gradient(160deg, #1e1e2f 0%, #2c2c3e 100%);">
    <div class="card shadow-lg border-0"
         style="background: linear-gradient(180deg, rgba(46,46,70,0.9) 0%, rgba(34,34,54,0.95) 100%);
                max-width: 420px; width: 100%; border-radius: 1.2rem;">
        <div class="card-body p-5">

            {{-- Logo + titre --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 60px;">
                <h2 class="fw-bold mt-3 text-light">Mot de passe oublié</h2>
                <p class="text-secondary small">Renseignez votre e-mail pour recevoir un lien de réinitialisation.</p>
            </div>

            {{-- Messages de succès ou d'erreur --}}
            @if ($errors->any())
                <div class="alert alert-danger text-center py-2">Adresse e-mail invalide.</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success text-center py-2">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulaire --}}
            <form action="" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label text-light fw-semibold">Adresse e-mail</label>
                    <input type="email" name="email" id="email"
                           class="form-control bg-dark text-light border-secondary"
                           placeholder="exemple@entreprise.com" required autofocus>
                </div>

                <button type="submit"
                        class="btn w-100 py-2 fw-semibold"
                        style="background-color: #4f46e5; border: none; color: white;">
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            {{-- Lien retour --}}
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="text-decoration-none text-light small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
