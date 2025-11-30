<div class="ticketing-admin-page" data-script="backlogsManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Tickets RH</h2>
            <p class="mb-0" style="font-size:.9rem;">
                Suivi des demandes (congés, notes de frais, documents RH, incidents…).
            </p>
        </div>
        <button id="btnAddTicket" class="btn btn-add-ticket">
            <i class="fa-solid fa-plus me-2"></i> Ajouter un ticket
        </button>
    </div>

    {{-- STATS --}}
    <div class="ticket-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p>—</p>
        </div>
    </div>

    {{-- FILTRES AVANCÉS (type + employé + statut + période + recherche) --}}
    <div class="ticket-filters-advanced mb-4">
        <div class="filters-row">

            {{-- Type de ticket --}}
            <div class="filter-group">
                <label for="filter-ticket-type" class="form-label">Type de ticket</label>
                <select id="filter-ticket-type" class="form-select">
                    <option value="">Tous</option>
                    <option value="conge">Congés</option>
                    <option value="note_frais">Notes de frais</option>
                    <option value="document_rh">Documents RH</option>
                    <option value="incident">Incidents</option>
                    <option value="autre">Autres</option>
                </select>
            </div>

            {{-- Employé (créateur) --}}
            {{--<div class="filter-group">
                <label for="filter-ticket-employee" class="form-label">Employé</label>
                <select id="filter-ticket-employee" class="form-select">
                    <option value="">Tous</option>
                    {{-- options dynamiques injectées en JS
                </select>
            </div> --}}

            {{-- Statut --}}
            <div class="filter-group">
                <label for="filter-ticket-status" class="form-label">Statut</label>
                <select id="filter-ticket-status" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide">Validé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>

            {{-- Période (date de création) --}}
            <div class="filter-group filter-group-period">
                <label class="form-label">Période</label>
                <div class="period-inputs">
                    <input type="date" id="filter-ticket-start" class="form-control">
                    <span class="period-separator">→</span>
                    <input type="date" id="filter-ticket-end" class="form-control">
                </div>
            </div>

            {{-- Recherche texte --}}
            <div class="filter-group filter-group-search">
                <label for="search-ticket" class="form-label">Recherche</label>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text"
                        id="filter-ticket-search"
                        class="input w-100"
                        placeholder="Rechercher un ticket...">
                </div>
            </div>

        </div>
    </div>


    {{-- LISTE DES TICKETS (dynamique) --}}
    <div class="ticket-list">
        <p class="text-muted p-3">Chargement des tickets...</p>
    </div>
</div>
