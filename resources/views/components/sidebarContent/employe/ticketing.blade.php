<div class="ticketing-employee-page" data-script="ticketEmployeeManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mes demandes RH</h2>
            <p class="text-muted mb-0" style="font-size:.9rem;">
                Consultez et suivez vos demandes de congés, notes de frais, documents RH, etc.
            </p>
        </div>

        <button id="btnAddTicket" class="btn btn-add-ticket">
            <i class="fa-solid fa-plus me-2"></i> Nouveau ticket RH
        </button>
    </div>

    {{-- FILTRES SIMPLIFIÉS --}}
    <div class="ticket-filters-advanced mb-4">
        <div class="filters-row">

            {{-- Type de ticket --}}
            <div class="filter-group">
                <label for="filter-ticket-type" class="form-label">Type</label>
                <select id="filter-ticket-type" class="form-select">
                    <option value="">Tous</option>
                    <option value="conge">Congés</option>
                    <option value="note_frais">Notes de frais</option>
                    <option value="document_rh">Documents RH</option>
                    <option value="incident">Incidents</option>
                    <option value="autre">Autres</option>
                </select>
            </div>

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

            {{-- Recherche --}}
            <div class="filter-group filter-group-search">
                <label for="filter-ticket-search" class="form-label">Recherche</label>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text"
                           id="filter-ticket-search"
                           class="search-input w-100"
                           placeholder="Rechercher dans mes tickets...">
                </div>
            </div>
        </div>
    </div>

    {{-- STATS (basées sur mes tickets seulement) --}}
    <div class="ticket-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>0</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p>0</p>
        </div>
    </div>

    {{-- LISTE DES TICKETS --}}
    <div class="ticket-list"></div>
</div>
