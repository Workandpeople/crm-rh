<div class="notes-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des notes de frais</h2>
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
    <div class="notes-stats mb-4">
        <div class="stat-card">
            <h6>Total soumis</h6>
            <p>56</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>12</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>38</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p>6</p>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="notes-filters mb-4">
        <div class="d-flex flex-wrap gap-2">
            <button class="filter-btn active">Tous</button>
            <button class="filter-btn">Déplacement</button>
            <button class="filter-btn">Repas</button>
            <button class="filter-btn">Hébergement</button>
            <button class="filter-btn">Autres</button>
        </div>
    </div>

    {{-- TABLEAU DES NOTES --}}
    <div class="notes-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Julien Dupont</strong></td>
                    <td>Repas</td>
                    <td>Déjeuner client Paris</td>
                    <td>23,90 €</td>
                    <td>10/10/2025</td>
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
                    <td>Déplacement</td>
                    <td>Trajet autoroute A86</td>
                    <td>8,40 €</td>
                    <td>08/10/2025</td>
                    <td><span class="status valide">Validé</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>Hébergement</td>
                    <td>Hôtel Lyon - séminaire</td>
                    <td>120,00 €</td>
                    <td>05/10/2025</td>
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
