<div class="societes-admin-page" data-script="companiesManagement">
  {{-- header --}}
  <div class="head-soc">
    <h2>Gestion des sociétés</h2>
    <button id="btnNewCompany" class="btn-add">
      <i class="fa-solid fa-building-circle-plus me-2"></i> Nouvelle société
    </button>
  </div>

  {{-- filtres (IDs conservés pour le JS) --}}
  <div class="societes-filters">
    <div class="fg">
      <label for="filter-admin">Administrateur</label>
      <select id="filter-admin" class="select">
        <option value="">Tous</option>
      </select>
    </div>

    <div class="fg">
      <label for="filter-search">Recherche</label>
      <input type="text" id="filter-search" class="input" placeholder="Nom, domaine…">
    </div>
  </div>

  {{-- liste de sociétés (cartes injectées par JS) --}}
  <div id="companiesList" class="societes-list"></div>

  {{-- pagination --}}
  <nav aria-label="Pagination sociétés">
    <ul id="companiesPagination" class="pagination pagination-sm mb-0"></ul>
  </nav>
</div>
