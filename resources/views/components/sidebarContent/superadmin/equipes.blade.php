<div class="equipe-admin-page" data-script="teamsManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des équipes</h2>
        <button id="btnNewTeam" class="btn btn-new-equipe">
            <i class="fa-solid fa-users-line me-2"></i> Nouvelle équipe
        </button>
    </div>

    {{-- FILTRES --}}
    <div class="equipe-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="fg">
                <label for="filter-societe">Société</label>
                <select id="filter-societe" class="select">
                    <option value="">Toutes</option>
                </select>
            </div>
        </div>
    </div>

    {{-- LISTE DES ÉQUIPES --}}
    <div id="teamsList" class="equipe-list">
        {{-- Cartes injectées dynamiquement par JS --}}
    </div>

    {{-- PAGINATION --}}
    <nav aria-label="Pagination équipes">
        <ul id="teamsPagination" class="pagination pagination-sm mb-0"></ul>
    </nav>
</div>
