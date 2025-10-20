<div class="paie-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des fiches de paie</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-upload">
                <i class="fa-solid fa-upload me-2"></i> Ajouter une fiche
            </button>
            <button class="btn btn-export">
                <i class="fa-solid fa-file-export me-2"></i> Exporter
            </button>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="paie-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="filter-group">
                <label for="filter-employe">Employé</label>
                <select id="filter-employe" class="form-select">
                    <option>Tous</option>
                    <option>Julien Dupont</option>
                    <option>Amélie Dubois</option>
                    <option>Marc Lefèvre</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-mois">Mois</label>
                <select id="filter-mois" class="form-select">
                    <option>Tous</option>
                    <option>Septembre 2025</option>
                    <option>Août 2025</option>
                    <option>Juillet 2025</option>
                </select>
            </div>
        </div>

    </div>

    {{-- TABLEAU DES FICHES --}}
    <div class="paie-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Mois</th>
                    <th>Date de mise en ligne</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Julien Dupont</strong></td>
                    <td>Septembre 2025</td>
                    <td>01/10/2025</td>
                    <td><span class="status disponible">Disponible</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action download"><i class="fa-solid fa-download"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Amélie Dubois</strong></td>
                    <td>Août 2025</td>
                    <td>01/09/2025</td>
                    <td><span class="status manquante">Non déposée</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action upload"><i class="fa-solid fa-upload"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>Juillet 2025</td>
                    <td>01/08/2025</td>
                    <td><span class="status archive">Archivée</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action download"><i class="fa-solid fa-download"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
