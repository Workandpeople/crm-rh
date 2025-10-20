<div class="ticketing-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mes Tickets RH</h2>
        <button class="btn btn-new-ticket">
            <i class="fa-solid fa-plus me-2"></i> Nouveau ticket
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="ticket-filters mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="filter-btn active" data-filter="all">Tous</button>
            <button class="filter-btn" data-filter="conge">Congés</button>
            <button class="filter-btn" data-filter="incident">Incidents</button>
            <button class="filter-btn" data-filter="note_frais">Notes de frais</button>
            <button class="filter-btn" data-filter="autre">Autres</button>
        </div>
    </div>

    {{-- LISTE DES TICKETS --}}
    <div class="ticket-list">
        <div class="ticket-card">
            <div class="ticket-header">
                <span class="ticket-type conge"><i class="fa-solid fa-plane-departure me-1"></i> Congé</span>
                <span class="ticket-status en-attente">En attente</span>
            </div>
            <h5 class="ticket-title">Demande de congé du 15/11 au 20/11</h5>
            <p class="ticket-desc">Motif : Vacances d’automne</p>
            <div class="ticket-footer">
                <small>Créé le 10/10/2025</small>
                <button class="btn-ticket-action">Détails</button>
            </div>
        </div>

        <div class="ticket-card">
            <div class="ticket-header">
                <span class="ticket-type note_frais"><i class="fa-solid fa-receipt me-1"></i> Note de frais</span>
                <span class="ticket-status valide">Validé</span>
            </div>
            <h5 class="ticket-title">Remboursement péage A86</h5>
            <p class="ticket-desc">Montant : 8,40 € - Justificatif envoyé</p>
            <div class="ticket-footer">
                <small>Créé le 05/10/2025</small>
                <button class="btn-ticket-action">Détails</button>
            </div>
        </div>

        <div class="ticket-card">
            <div class="ticket-header">
                <span class="ticket-type incident"><i class="fa-solid fa-triangle-exclamation me-1"></i> Incident</span>
                <span class="ticket-status en-cours">En cours</span>
            </div>
            <h5 class="ticket-title">Problème avec la tablette de travail</h5>
            <p class="ticket-desc">L’écran ne répond plus, signalé au service IT.</p>
            <div class="ticket-footer">
                <small>Créé le 02/10/2025</small>
                <button class="btn-ticket-action">Détails</button>
            </div>
        </div>
    </div>
</div>
