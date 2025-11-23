<div class="documents-admin-page" data-script="documentsManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Documents RH</h2>
        <button class="btn btn-export">
            <i class="fa-solid fa-file-export me-2"></i> Exporter
        </button>
    </div>

    {{-- STATISTIQUES (remplies en JS) --}}
    <div class="document-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Refusés / Expirés</h6>
            <p>—</p>
        </div>
    </div>

    {{-- FILTRES AVANCÉS --}}
    <div class="documents-filters mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-end justify-content-between">

            <div class="d-flex flex-wrap gap-3">
                {{-- Employé --}}
                <div class="filter-group">
                    <label for="filter-document-employee" class="form-label">Employé</label>
                    <select id="filter-document-employee" class="form-select">
                        <option value="">Tous</option>
                        {{-- options dynamiques injectées en JS --}}
                    </select>
                </div>

                {{-- Type de document --}}
                <div class="filter-group">
                    <label for="filter-document-type" class="form-label">Type de document</label>
                    <select id="filter-document-type" class="form-select">
                        <option value="">Tous</option>
                        <option value="CNI">Carte d’identité</option>
                        <option value="Carte Vitale">Carte Vitale</option>
                        <option value="Permis">Permis de conduire</option>
                        <option value="Contrat">Contrat</option>
                        <option value="Fiche Fonction">Fiche Fonction</option>
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filter-group">
                    <label for="filter-document-status" class="form-label">Statut</label>
                    <select id="filter-document-status" class="form-select">
                        <option value="">Tous</option>
                        <option value="validated">Validé</option>
                        <option value="pending">En attente</option>
                        <option value="rejected">Refusé</option>
                        <option value="expired">Expiré</option>
                    </select>
                </div>
            </div>

            {{-- 🔍 BARRE DE RECHERCHE (nom/prénom) --}}
            <div class="search-bar">
                <label for="search-document" class="form-label d-none">Recherche</label>
                <div class="position-relative">
                    <input type="text"
                           id="search-document"
                           class="form-control ps-4"
                           placeholder="Rechercher un employé...">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES DOCUMENTS (dynamique) --}}
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
            <tbody id="documentsTableBody">
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        Chargement des documents...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
