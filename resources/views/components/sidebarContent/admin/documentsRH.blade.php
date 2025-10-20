<div class="documents-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Documents RH</h2>
        <button class="btn btn-export">
            <i class="fa-solid fa-file-export me-2"></i> Exporter
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="documents-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-end justify-content-between">
            <div class="d-flex flex-wrap gap-3">
                {{-- Filtres employés / types / statuts --}}
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
                    <label for="filter-type">Type de document</label>
                    <select id="filter-type" class="form-select">
                        <option>Tous</option>
                        <option>Carte d’identité</option>
                        <option>Certificat médical</option>
                        <option>Permis de conduire</option>
                        <option>Contrat</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter-statut">Statut</label>
                    <select id="filter-statut" class="form-select">
                        <option>Tous</option>
                        <option>Validé</option>
                        <option>En attente</option>
                        <option>Refusé</option>
                        <option>Expiré</option>
                    </select>
                </div>
            </div>

            {{-- 🔍 BARRE DE RECHERCHE --}}
            <div class="search-bar">
                <input type="text" id="search-employe" placeholder="Rechercher un employé..." />
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
    </div>



    {{-- STATISTIQUES --}}
    <div class="document-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p>120</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>78</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>25</p>
        </div>
        <div class="stat-card">
            <h6>Refusés / Expirés</h6>
            <p>17</p>
        </div>
    </div>

    {{-- TABLEAU DES DOCUMENTS --}}
    <div class="documents-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type de document</th>
                    <th>Date dépôt</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Julien Dupont</strong></td>
                    <td>Carte d’identité</td>
                    <td>10/10/2025</td>
                    <td><span class="status valide">Validé</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action download"><i class="fa-solid fa-download"></i></button>
                            <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Amélie Dubois</strong></td>
                    <td>Certificat médical</td>
                    <td>—</td>
                    <td><span class="status en-attente">En attente</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action valide"><i class="fa-solid fa-check"></i></button>
                            <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>Permis de conduire</td>
                    <td>15/09/2025</td>
                    <td><span class="status expire">Expiré</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action upload"><i class="fa-solid fa-upload"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
