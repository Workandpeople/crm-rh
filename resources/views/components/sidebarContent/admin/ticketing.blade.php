<div class="ticketing-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tickets RH</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-filter">
                <i class="fa-solid fa-filter me-2"></i> Filtres
            </button>
            <button class="btn btn-export">
                <i class="fa-solid fa-file-export me-2"></i> Exporter
            </button>
        </div>
    </div>

    {{-- STATS RAPIDES --}}
    <div class="ticket-stats mb-4">
        <div class="stat-card">
            <h6>Total tickets</h6>
            <p>32</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>8</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>18</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p>6</p>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="ticket-filters mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="filter-btn active">Tous</button>
            <button class="filter-btn">Congés</button>
            <button class="filter-btn">Notes de frais</button>
            <button class="filter-btn">Incidents</button>
            <button class="filter-btn">Autres</button>
        </div>
    </div>

    {{-- LISTE DES TICKETS --}}
    <div class="ticket-list">
        <div class="ticket-card">
            <div class="ticket-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="ticket-type conge"><i class="fa-solid fa-plane-departure"></i> Congé</span>
                    <span class="ticket-user">Julien Dupont</span>
                </div>
                <span class="ticket-status en-attente">En attente</span>
            </div>

            <h5 class="ticket-title">Demande de congé du 12/11 au 18/11</h5>
            <p class="ticket-desc">Motif : Vacances d’automne</p>

            <div class="ticket-footer">
                <small>Créé le 10/10/2025</small>
                <div class="actions">
                    <button class="btn-action valide"><i class="fa-solid fa-check"></i></button>
                    <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                    <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
        </div>

        <div class="ticket-card">
            <div class="ticket-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="ticket-type note_frais"><i class="fa-solid fa-receipt"></i> Note de frais</span>
                    <span class="ticket-user">Amélie Dubois</span>
                </div>
                <span class="ticket-status valide">Validé</span>
            </div>

            <h5 class="ticket-title">Remboursement péage A86</h5>
            <p class="ticket-desc">Montant : 8,40 € - Justificatif transmis</p>

            <div class="ticket-footer">
                <small>Créé le 03/10/2025</small>
                <div class="actions">
                    <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
