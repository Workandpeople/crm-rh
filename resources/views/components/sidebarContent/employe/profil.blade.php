<div class="profil-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mon Profil</h2>
        <button class="btn btn-edit-profile">
            <i class="fa-solid fa-pen me-2"></i> Modifier mon profil
        </button>
    </div>

    @php
        $user = auth()->user();
        $profile = $user->profile;
        $company = $user->company;
        $team = $user->team;
        $display = function ($value, $hasField = true) {
            if (! $hasField) {
                return 'not in bdd';
            }
            if (is_null($value) || $value === '') {
                return 'to be defined';
            }
            return $value;
        };
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="profil-avatar">
                    <img src="{{ asset('images/avatar.png') }}" alt="Avatar">
                    <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p>{{ $user->email }}</p>
                    <span class="badge mt-3">{{ ucfirst($display($user->status)) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="fw-semibold mb-3">Informations personnelles</h5>
                <div class="profil-info">
                    <div class="profil-info-item">
                        <label>Nom complet</label>
                        <p>{{ $display(trim($user->first_name.' '.$user->last_name)) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Adresse e-mail</label>
                        <p>{{ $display($user->email) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Poste</label>
                        <p>{{ $display($profile->position ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Société</label>
                        <p>{{ $display($company->name ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Équipe</label>
                        <p>{{ $display($team->name ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Date d'embauche</label>
                        <p>{{ $display(optional($profile)->hire_date?->format('d/m/Y')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <h5 class="fw-semibold mb-3">Complétude du dossier RH</h5>
        <div class="profil-progress">
            <div class="bar" style="width: 78%;">78%</div>
        </div>
        <p class="mt-2 small" style="color: var(--color-text-muted);">
            Documents manquants : CNI, Fiche de fonction, Certificat médical
        </p>
    </div>

    <div class="profil-actions">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-folder-open"></i>
                <h6 class="mb-0">Voir mon dossier RH</h6>
            </div>
            <a href="#" class="link-dynamic" data-page="dossierRH">Ouvrir</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-plane-departure"></i>
                <h6 class="mb-0">Faire une demande de congé</h6>
            </div>
            <a href="#" class="link-dynamic" data-page="ticketing">Accéder</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-ticket"></i>
                <h6 class="mb-0">Ouvrir un ticket RH</h6>
            </div>
            <a href="#" class="link-dynamic" data-page="ticketing">Créer</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-receipt"></i>
                <h6 class="mb-0">Fiche de Paie</h6>
            </div>
            <a href="https://monespacepaye.example.com" target="_blank" rel="noopener" class="link-dynamic">Consulter</a>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('click', (e) => {
            const target = e.target.closest('.link-dynamic');
            if (target) {
                e.preventDefault();
                loadContent(target.dataset.page);
            }
        });
    </script>
@endpush
