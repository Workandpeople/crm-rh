<div class="profil-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mon Profil</h2>
        <button class="btn btn-edit-profile">
            <i class="fa-solid fa-pen me-2"></i> Modifier mon profil
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="profil-avatar">
                    <img src="{{ asset('images/avatar.png') }}" alt="Avatar">
                    <h5>{{ auth()->user()->name }}</h5>
                    <p>{{ auth()->user()->email }}</p>
                    <span class="badge mt-3">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="fw-semibold mb-3">Informations personnelles</h5>
                <div class="profil-info">
                    <div class="profil-info-item">
                        <label>Nom complet</label>
                        <p>{{ auth()->user()->name }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Adresse e-mail</label>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Poste</label>
                        <p>Technicien contrôle</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Société</label>
                        <p>Work And People</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Équipe</label>
                        <p>Équipe Nord</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Date d'embauche</label>
                        <p>15/03/2022</p>
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
            <i class="fa-solid fa-folder-open"></i>
            <h6>Voir mon dossier RH</h6>
            <a href="#" class="link-dynamic" data-page="dossierRH">Ouvrir</a>
        </div>
        <div class="card p-4">
            <i class="fa-solid fa-plane-departure"></i>
            <h6>Faire une demande de congé</h6>
            <a href="#" class="link-dynamic" data-page="conge">Accéder</a>
        </div>
        <div class="card p-4">
            <i class="fa-solid fa-ticket"></i>
            <h6>Ouvrir un ticket RH</h6>
            <a href="#" class="link-dynamic" data-page="ticketing">Créer</a>
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
