<div class="ticketing-admin-page" data-script="backlogsManagement">
  {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Tickets RH</h2>
        <p class="text-muted mb-0" style="font-size: .9rem;">
        Suivi des demandes (congés, notes de frais, incidents…) et validation RH.
        </p>
    </div>
    <button id="btnAddTicket" class="btn btn-add-ticket">
        <i class="fa-solid fa-plus me-2"></i> Ajouter un ticket
    </button>
    </div>


  {{-- STATS (placeholder) --}}
  <div class="ticket-stats mb-4">
    <div class="stat-card"><h6>Total</h6><p>–</p></div>
    <div class="stat-card"><h6>En attente</h6><p>–</p></div>
    <div class="stat-card"><h6>Validés</h6><p>–</p></div>
    <div class="stat-card"><h6>Refusés</h6><p>–</p></div>
  </div>

  {{-- FILTRES --}}
  <div class="ticket-filters mb-4">
    <div class="d-flex flex-wrap gap-2">
      <button class="filter-btn active" data-type="all">Tous</button>
      <button class="filter-btn" data-type="conge">Congés</button>
      <button class="filter-btn" data-type="note_frais">Notes de frais</button>
      <button class="filter-btn" data-type="incident">Incidents</button>
      <button class="filter-btn" data-type="autre">Autres</button>
    </div>
  </div>

  {{-- LISTE DES TICKETS (dynamique) --}}
  <div class="ticket-list">
    <p class="text-muted p-3">Chargement des tickets...</p>
  </div>
</div>
