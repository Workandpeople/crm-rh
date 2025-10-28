<div class="user-management-page" data-script="usersManagement">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des utilisateurs</h2>
        <button id="btnNewUser" class="btn btn-new-user">
            <i class="fa-solid fa-user-plus me-2"></i> Nouvel utilisateur
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="user-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">

            <div class="filter-group">
                <label for="filter-role">Rôle</label>
                <select id="filter-role" class="form-select">
                    <option value="">Tous</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="chef_equipe">Chef d’équipe</option>
                    <option value="employe">Employé</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-societe">Société</label>
                <select id="filter-societe" class="form-select">
                    <option value="">Toutes</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-statut">Statut</label>
                <select id="filter-statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="pending">En attente</option>
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
            <tbody id="usersTableBody">
                <tr>
                    <td colspan="7" class="text-center py-4">Chargement...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
