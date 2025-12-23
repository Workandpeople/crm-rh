<div class="ticketing-employee-page" data-script="ticketingEmployee">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Mes tickets RH</h2>
            <p class="mb-0" style="font-size:.9rem;">
                Suivi de vos demandes (congés, notes de frais, documents RH, incidents…)
            </p>
        </div>

        <button class="btn btn-primary" id="btnOpenCreateTicket">
            <i class="fa-solid fa-plus me-2"></i> Nouveau ticket
        </button>
    </div>

    {{-- filtres employé (simple) --}}
    <div class="ticketing-employee-filters mb-3">
        <div class="filters-row">
            <div class="filter-group">
                <label class="form-label">Type</label>
                <select id="filterEmployeeType" class="form-select">
                    <option value="">Tous</option>
                    <option value="conge">Congés</option>
                    <option value="note_frais">Notes de frais</option>
                    <option value="document_rh">Documents RH</option>
                    <option value="incident">Incidents</option>
                    <option value="autre">Autres</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="form-label">Statut</label>
                <select id="filterEmployeeStatus" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide">Validé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>

            <div class="filter-group filter-group-search">
                <label class="form-label">Recherche</label>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input id="filterEmployeeSearch" type="text" class="form-control" placeholder="Titre / description…">
                </div>
            </div>
        </div>
    </div>

    {{-- stats rapides --}}
    <div class="ticketing-employee-stats mb-3">
        <div class="stat-card">
            <div class="stat-label">Total</div>
            <div class="stat-value" id="statTotal">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">En attente</div>
            <div class="stat-value" id="statPending">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Validés</div>
            <div class="stat-value" id="statValidated">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Refusés</div>
            <div class="stat-value" id="statRefused">—</div>
        </div>
    </div>

    {{-- liste --}}
    <div class="ticketing-employee-list" id="employeeTicketsList">
        <p class="text-muted p-3">Chargement…</p>
    </div>
</div>
