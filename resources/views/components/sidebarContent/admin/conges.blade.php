<div class="conges-admin-page" data-script="congesManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Gestion des congés</h2>
            <p class=" mb-0" style="font-size: .9rem;">
                Suivez et validez les demandes de congés et absences pour la société sélectionnée.
            </p>
        </div>
        <div class="d-flex gap-2">
            {{-- On garde Export pour plus tard (CSV / Excel) --}}
            <button class="btn btn-export">
                <i class="fa-solid fa-file-export me-2"></i> Exporter
            </button>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES (remplies en JS) --}}
    <div class="conge-stats mb-4">
        <div class="stat-card">
            <h6>Total demandes</h6>
            <p id="statTotalConges">—</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p id="statPendingConges">—</p>
        </div>
        <div class="stat-card">
            <h6>Validées</h6>
            <p id="statValidatedConges">—</p>
        </div>
        <div class="stat-card">
            <h6>Refusées</h6>
            <p id="statRefusedConges">—</p>
        </div>
    </div>

    {{-- FILTRES PAR TYPE DE CONGÉ (chips) --}}
    <div class="conge-filters mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="filter-btn active" data-kind="all">Tous</button>
            <button class="filter-btn" data-kind="conges_payes">Congés payés</button>
            <button class="filter-btn" data-kind="rtt">RTT</button>
            <button class="filter-btn" data-kind="sans_solde">Sans solde</button>
            <button class="filter-btn" data-kind="absence_exceptionnelle">Absence exceptionnelle</button>
        </div>
    </div>

    {{-- TABLEAU DES DEMANDES (dynamique) --}}
    <div class="conge-table">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="congesTableBody">
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Chargement des demandes...
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <nav aria-label="Pagination des congés" class="mt-2">
            <ul class="pagination justify-content-center mb-0" id="congesPagination"></ul>
        </nav>
    </div>
</div>
