<div class="entretiens-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des entretiens</h2>
        <button class="btn btn-new-entretien">
            <i class="fa-solid fa-plus me-2"></i> Nouvel entretien
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="entretien-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="filter-group">
                <label for="filter-type">Type d’entretien</label>
                <select id="filter-type" class="form-select">
                    <option>Tous</option>
                    <option>Annuel</option>
                    <option>Professionnel</option>
                    <option>Embauche</option>
                    <option>Disciplinaires</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-statut">Statut</label>
                <select id="filter-statut" class="form-select">
                    <option>Tous</option>
                    <option>À venir</option>
                    <option>Terminé</option>
                    <option>Annulé</option>
                </select>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES ENTRETIENS --}}
    <div class="entretien-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Statut</th>
                    <th>Responsable</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Julien Dupont</strong></td>
                    <td>Annuel</td>
                    <td>10/11/2025</td>
                    <td>09h30</td>
                    <td><span class="status avenir">À venir</span></td>
                    <td>Amélie Dubois</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>Professionnel</td>
                    <td>02/10/2025</td>
                    <td>14h00</td>
                    <td><span class="status termine">Terminé</span></td>
                    <td>Claire Bernard</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action details"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Sophie Martin</strong></td>
                    <td>Disciplinaires</td>
                    <td>05/10/2025</td>
                    <td>11h00</td>
                    <td><span class="status annule">Annulé</span></td>
                    <td>Amélie Dubois</td>
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
