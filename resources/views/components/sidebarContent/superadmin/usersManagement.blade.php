<div class="user-management-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des utilisateurs</h2>
        <button class="btn btn-new-user">
            <i class="fa-solid fa-user-plus me-2"></i> Nouvel utilisateur
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="user-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="filter-group">
                <label for="filter-role">Rôle</label>
                <select id="filter-role" class="form-select">
                    <option>Tous</option>
                    <option>Super Admin</option>
                    <option>Admin</option>
                    <option>Employé</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-societe">Société</label>
                <select id="filter-societe" class="form-select">
                    <option>Toutes</option>
                    <option>Genius Contrôle</option>
                    <option>Work and People</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-statut">Statut</label>
                <select id="filter-statut" class="form-select">
                    <option>Tous</option>
                    <option>Actif</option>
                    <option>Inactif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- TABLEAU UTILISATEURS --}}
    <div class="user-table">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Société</th>
                    <th>Statut</th>
                    <th>Dernière connexion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Jean Martin</strong></td>
                    <td>jean.martin@geniuscontrole.fr</td>
                    <td><span class="role superadmin">Super Admin</span></td>
                    <td>Genius Contrôle</td>
                    <td><span class="status actif">Actif</span></td>
                    <td>12/10/2025 à 09h23</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Amélie Dubois</strong></td>
                    <td>amelie.rh@workandpeople.fr</td>
                    <td><span class="role admin">Admin</span></td>
                    <td>Work and People</td>
                    <td><span class="status actif">Actif</span></td>
                    <td>11/10/2025 à 17h42</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><strong>Marc Lefèvre</strong></td>
                    <td>marc.lefevre@geniuscontrole.fr</td>
                    <td><span class="role employe">Employé</span></td>
                    <td>Genius Contrôle</td>
                    <td><span class="status inactif">Inactif</span></td>
                    <td>02/09/2025 à 10h10</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
