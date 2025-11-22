<div class="notes-admin-page" data-script="expensesManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des notes de frais</h2>
        <div class="d-flex gap-2">
            {{-- Le bouton filtres pourra ouvrir une modale plus tard si besoin --}}
            <button class="btn btn-filter">
                <i class="fa-solid fa-filter me-2"></i> Filtres
            </button>
            <button class="btn btn-export">
                <i class="fa-solid fa-file-export me-2"></i> Exporter
            </button>
        </div>
    </div>

    {{-- STATS RAPIDES (remplies en JS) --}}
    <div class="notes-stats mb-4">
        <div class="stat-card">
            <h6>Total soumis</h6>
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

    {{-- FILTRES AVANCÉS --}}
    <div class="notes-advanced-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-end">
            <div class="filter-group">
                <label for="filter-expense-employee" class="form-label">Employé</label>
                <select id="filter-expense-employee" class="form-select">
                    <option value="">Tous</option>
                    {{-- options dynamiques JS --}}
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-expense-type" class="form-label">Type</label>
                <select id="filter-expense-type" class="form-select">
                    <option value="">Tous</option>
                    <option value="peage">Péage / autoroute</option>
                    <option value="repas">Repas</option>
                    <option value="hebergement">Hébergement</option>
                    <option value="km">Kilométrage</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-expense-status" class="form-label">Statut</label>
                <select id="filter-expense-status" class="form-select">
                    <option value="">Tous</option>
                    <option value="pending">En attente</option>
                    <option value="approved">Validé</option>
                    <option value="rejected">Refusé</option>
                    <option value="paid">Payé</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-expense-start" class="form-label">Du</label>
                <input type="date" id="filter-expense-start" class="form-control">
            </div>

            <div class="filter-group">
                <label for="filter-expense-end" class="form-label">Au</label>
                <input type="date" id="filter-expense-end" class="form-control">
            </div>

            <div class="filter-group">
                <button id="btnExpensesReset" type="button" class="btn btn-outline-secondary">
                    Réinitialiser
                </button>
            </div>
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
                    <th>Justificatif</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="expensesTableBody">
                <tr>
                    <td colspan="8" class="text-center  py-3">
                        Chargement des notes de frais...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
