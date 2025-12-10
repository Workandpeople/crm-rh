<div class="ticketing-admin-page" data-script="backlogsManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Tickets RH</h2>
            <p class="mb-0" style="font-size:.9rem;">
                Suivi des demandes (congés, notes de frais, documents RH, incidents…).
            </p>
        </div>
        <button id="btnAddTicket" class="btn btn-add-ticket">
            <i class="fa-solid fa-plus me-2"></i> Ajouter un ticket
        </button>
    </div>

    {{-- STATS --}}
    <div class="ticket-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p>—</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p>—</p>
        </div>
    </div>

  {{-- FILTRES AVANCÉS (type + statut + filtres contextuels + recherche) --}}
    <div class="ticket-filters-advanced mb-4">
        <div class="filters-row">

            {{-- Type de ticket --}}
            <div class="filter-group">
                <label for="filter-ticket-type" class="form-label">Type de ticket</label>
                <select id="filter-ticket-type" class="form-select">
                    <option value="">Tous</option>
                    <option value="conge">Congés</option>
                    <option value="note_frais">Notes de frais</option>
                    <option value="document_rh">Documents RH</option>
                    <option value="incident">Incidents</option>
                    <option value="autre">Autres</option>
                </select>
            </div>

            {{-- Statut --}}
            <div class="filter-group">
                <label for="filter-ticket-status" class="form-label">Statut</label>
                <select id="filter-ticket-status" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide">Validé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>

            {{-- 🔁 FILTRES SPÉCIFIQUES AU TYPE DE TICKET --}}
            {{-- CONGÉS : période de congé --}}
            <div class="filter-group filter-group-extra d-none" data-extra-type="conge">
                <label class="form-label">Période de congé</label>
                <div class="period-inputs">
                    <input type="date"
                        id="filter-leave-start"
                        class="form-control"
                        placeholder="Du">
                    <span class="period-separator">→</span>
                    <input type="date"
                        id="filter-leave-end"
                        class="form-control"
                        placeholder="Au">
                </div>
            </div>

            {{-- NOTES DE FRAIS : montant --}}
            <div class="filter-group filter-group-extra d-none" data-extra-type="note_frais">
                <label class="form-label">Montant (€)</label>
                <div class="period-inputs">
                    <input type="number"
                        step="0.01"
                        min="0"
                        id="filter-expense-min"
                        class="form-control"
                        placeholder="Min">
                    <span class="period-separator">→</span>
                    <input type="number"
                        step="0.01"
                        min="0"
                        id="filter-expense-max"
                        class="form-control"
                        placeholder="Max">
                </div>
            </div>

            {{-- DOCUMENTS RH : type de document --}}
            <div class="filter-group filter-group-extra d-none" data-extra-type="document_rh">
                <label for="filter-document-type" class="form-label">Type de document</label>
                <select id="filter-document-type" class="form-select">
                    <option value="">Tous</option>
                    <option value="CNI">Carte d’identité</option>
                    <option value="Carte Vitale">Carte Vitale</option>
                    <option value="Permis">Permis de conduire</option>
                    <option value="Contrat">Contrat de travail</option>
                    <option value="Fiche Fonction">Fiche de fonction</option>
                </select>
            </div>

            {{-- INCIDENTS : gravité --}}
            <div class="filter-group filter-group-extra d-none" data-extra-type="incident">
                <label for="filter-incident-severity" class="form-label">Gravité</label>
                <select id="filter-incident-severity" class="form-select">
                    <option value="">Toutes</option>
                    <option value="mineur">Mineur</option>
                    <option value="majeur">Majeur</option>
                    <option value="critique">Critique</option>
                </select>
            </div>

            {{-- Recherche texte --}}
            <div class="filter-group filter-group-search">
                <label for="filter-ticket-search" class="form-label">Recherche</label>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text"
                        id="filter-ticket-search"
                        class="search-input w-100"
                        placeholder="Rechercher un ticket...">
                </div>
            </div>

        </div>
    </div>



    {{-- LISTE DES TICKETS (dynamique) --}}
    <div class="ticket-list">
        <p class="text-muted p-3">Chargement des tickets...</p>
    </div>
</div>
