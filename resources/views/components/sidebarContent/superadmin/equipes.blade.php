<div class="equipe-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des équipes</h2>
        <button class="btn btn-new-equipe">
            <i class="fa-solid fa-users-line me-2"></i> Nouvelle équipe
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="equipe-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="filter-group">
                <label for="filter-societe">Société</label>
                <select id="filter-societe" class="form-select">
                    <option>Toutes</option>
                    <option>Genius Contrôle</option>
                    <option>Work and People</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-departement">Département</label>
                <select id="filter-departement" class="form-select">
                    <option>Tous</option>
                    <option>Technique</option>
                    <option>Commercial</option>
                    <option>RH</option>
                    <option>Support</option>
                </select>
            </div>
        </div>
    </div>

    {{-- LISTE DES ÉQUIPES --}}
    <div class="equipe-list">
        <div class="equipe-card">
            <div class="equipe-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-semibold mb-0">Équipe Technique</h4>
                <span class="badge societe">Genius Contrôle</span>
            </div>

            <p class="mb-2"><i class="fa-solid fa-user-tie me-2 text-primary"></i> Chef d’équipe : <strong>Jean Martin</strong></p>
            <p class="mb-3"><i class="fa-solid fa-users me-2 text-primary"></i> Membres : 6 employés</p>

            {{-- LISTE DES MEMBRES --}}
            <div class="members-list">
                <span class="member">Julien Dupont</span>
                <span class="member">Paul Leroy</span>
                <span class="member">Julie Thomas</span>
                <span class="member">Luc Dubois</span>
            </div>

            {{-- ACTIONS --}}
            <div class="actions mt-3">
                <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>

        <div class="equipe-card">
            <div class="equipe-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-semibold mb-0">Équipe d'Audit</h4>
                <span class="badge societe">Work and People</span>
            </div>

            <p class="mb-2"><i class="fa-solid fa-user-tie me-2 text-primary"></i> Chef d’équipe : <strong>Amélie Dubois</strong></p>
            <p class="mb-3"><i class="fa-solid fa-users me-2 text-primary"></i> Membres : 4 employés</p>

            <div class="members-list">
                <span class="member">Sophie Martin</span>
                <span class="member">Claire Bernard</span>
                <span class="member">Marc Lefèvre</span>
            </div>

            <div class="actions mt-3">
                <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
    </div>
</div>
