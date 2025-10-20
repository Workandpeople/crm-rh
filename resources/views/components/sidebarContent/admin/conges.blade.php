<div class="conges-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des congés</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-filter">
                <i class="fa-solid fa-filter me-2"></i> Filtres
            </button>
            <button class="btn btn-export">
                <i class="fa-solid fa-file-export me-2"></i> Exporter
            </button>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="conge-stats mb-4">
        <div class="stat-card">
            <h6>Total demandes</h6>
            <p>42</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>8</p>
        </div>
        <div class="stat-card">
            <h6>Validées</h6>
            <p>30</p>
        </div>
        <div class="stat-card">
            <h6>Refusées</h6>
            <p>4</p>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="conge-filters mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="filter-btn active">Tous</button>
            <button class="filter-btn">Congés payés</button>
            <button class="filter-btn">RTT</button>
            <button class="filter-btn">Sans solde</button>
            <button class="filter-btn">Absence exceptionnelle</button>
        </div>
    </div>

    {{-- TABLEAU DES DEMANDES --}}
    <div class="conge-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Julien Dupont</strong></td>
                    <td>Congé payé</td>
                    <td>15/11/2025 → 20/11/2025</td>
                    <td>5 jours</td>
                    <td><span class="status en-attente">En attente</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action valide"><i class="fa-solid fa-check"></i></button>
                            <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Amélie Dubois</strong></td>
                    <td>RTT</td>
                    <td>05/09/2025 → 06/09/2025</td>
                    <td>2 jours</td>
                    <td><span class="status valide">Validé</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>Sans solde</td>
                    <td>02/10/2025 → 04/10/2025</td>
                    <td>3 jours</td>
                    <td><span class="status refuse">Refusé</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
