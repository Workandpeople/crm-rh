<div class="sa-users" data-script="usersManagement">
  {{-- header --}}
  <div class="head">
    <div>
      <h2>Gestion des utilisateurs</h2>
      <p class="subhead">Visualisez, filtrez et gérez l’ensemble des utilisateurs de votre CRM RH.</p>
    </div>
    <button id="btnNewUser" class="btn-add">
      <i class="fa-solid fa-user-plus me-2"></i> Nouvel utilisateur
    </button>
  </div>

  {{-- filtres --}}
  <div class="filters">
    <div class="fg search-field">
      <label for="filter-search">Recherche</label>
      <div class="input-icon">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="filter-search" class="input" placeholder="Nom ou prénom…">
      </div>
    </div>

    <div class="fg">
      <label for="filter-role">Rôle</label>
      <select id="filter-role" class="select">
        <option value="">Tous</option>
        <option value="superadmin">Super Admin</option>
        <option value="admin">Admin</option>
        <option value="chef_equipe">Chef d’équipe</option>
        <option value="employe">Employé</option>
      </select>
    </div>

    <div class="fg">
      <label for="filter-societe">Société</label>
      <select id="filter-societe" class="select">
        <option value="">Toutes</option>
      </select>
    </div>

    <div class="fg">
      <label for="filter-statut">Statut</label>
      <select id="filter-statut" class="select">
        <option value="">Tous</option>
        <option value="active">Actif</option>
        <option value="inactive">Inactif</option>
        <option value="pending">En attente</option>
      </select>
    </div>
  </div>

  {{-- table --}}
  <div class="tbl-wrap">
    <table class="tbl">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Société</th>
          <th>Statut</th>
          <th>Dernière connexion</th>
          <th class="col-actions">Actions</th>
        </tr>
      </thead>
      <tbody id="usersTableBody">
        <tr><td colspan="7" class="empty">Chargement…</td></tr>
      </tbody>
    </table>

    {{-- pagination --}}
    <nav aria-label="Pagination utilisateurs">
      <ul class="pagination" id="usersPagination"></ul>
    </nav>
  </div>
</div>
