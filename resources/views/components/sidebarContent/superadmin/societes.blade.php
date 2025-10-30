<div class="societes-admin-page" data-script="companiesManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des sociétés</h2>
        <button id="btnNewCompany" class="btn btn-new-societe">
            <i class="fa-solid fa-building-circle-plus me-2"></i> Nouvelle société
        </button>
    </div>

    {{-- FILTRES (style identique aux users) --}}
    <div class="user-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="filter-group">
                <label for="filter-admin">Administrateur</label>
                <select id="filter-admin" class="form-select">
                    <option value="">Tous</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-search">Recherche</label>
                <input type="text" id="filter-search" class="form-control" placeholder="Nom, domaine…">
            </div>
        </div>
    </div>

    {{-- LISTE DES SOCIÉTÉS --}}
    <div id="companiesList" class="societes-list">
        {{-- Cartes injectées par JS --}}
    </div>

    {{-- Pagination --}}
    <nav aria-label="Pagination sociétés" class="mt-3">
        <ul id="companiesPagination" class="pagination pagination-sm mb-0"></ul>
    </nav>
</div>
