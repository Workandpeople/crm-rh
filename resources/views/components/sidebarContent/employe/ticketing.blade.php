<div
    class="ticketing-admin-page ticketing-employee-page"
    data-script="ticketingEmployee"
    data-company-id="{{ auth()->user()->company_id }}"
    data-user-id="{{ auth()->id() }}"
>
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1">Mes Tickets RH</h2>
            <p class="mb-0 text-muted" style="font-size:.92rem;">
                Vue personnelle : vos tickets créés ou qui vous sont assignés.
            </p>
        </div>
        <button id="btnEmployeeAddTicket" class="btn btn-add-ticket">
            <i class="fa-solid fa-plus me-2"></i> Nouveau ticket
        </button>
    </div>

    {{-- STATS --}}
    <div class="ticket-stats mb-4">
        <div class="stat-card">
            <h6>Total</h6>
            <p id="stat-total">—</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p id="stat-pending">—</p>
        </div>
        <div class="stat-card">
            <h6>Validés</h6>
            <p id="stat-validated">—</p>
        </div>
        <div class="stat-card">
            <h6>Refusés</h6>
            <p id="stat-refused">—</p>
        </div>
    </div>

    {{-- FILTRES SIMPLIFIÉS --}}
    <div class="ticket-filters-advanced mb-4">
        <div class="filters-row">
            <div class="filter-group">
                <label for="filter-ticket-type-me" class="form-label">Type</label>
                <select id="filter-ticket-type-me" class="form-select">
                    <option value="">Tous</option>
                    <option value="conge">Congés</option>
                    <option value="note_frais">Notes de frais</option>
                    <option value="document_rh">Documents RH</option>
                    <option value="incident">Incidents</option>
                    <option value="autre">Autres</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-ticket-status-me" class="form-label">Statut</label>
                <select id="filter-ticket-status-me" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide">Validé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>

            <div class="filter-group filter-group-period">
                <label class="form-label">Période</label>
                <div class="period-inputs">
                    <input type="date" id="filter-ticket-start-me" class="form-control">
                    <span class="period-separator">→</span>
                    <input type="date" id="filter-ticket-end-me" class="form-control">
                </div>
            </div>

            <div class="filter-group filter-group-search">
                <label for="filter-ticket-search-me" class="form-label">Recherche</label>
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="filter-ticket-search-me" class="input w-100" placeholder="Titre, description…">
                </div>
            </div>
        </div>
    </div>

    {{-- LISTE DES TICKETS --}}
    <div class="ticket-list" id="ticketListMe">
        <p class="text-muted p-3">Chargement des tickets...</p>
    </div>
</div>

{{-- MODALE CRÉATION --}}
<div class="modal fade" id="modalTicketCreateMe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formCreateTicketMe">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Nouveau ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="company_id" id="ticketCompanyMe" value="{{ auth()->user()->company_id }}">

                    <div class="mb-3">
                        <label class="form-label">Type de demande</label>
                        <select name="type" class="form-select" required>
                            <option value="conge">Congé / absence</option>
                            <option value="note_frais">Note de frais</option>
                            <option value="document_rh">Document RH</option>
                            <option value="incident">Incident</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ex : Demande de congé du 12 au 18 mars">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Précisez le contexte, les dates, les détails nécessaires…"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priorité</label>
                            <select name="priority" class="form-select">
                                <option value="basse">Basse</option>
                                <option value="moyenne" selected>Moyenne</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Échéance</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALE DÉTAIL --}}
<div class="modal fade modal-dark" id="modalTicketDetailsMe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-ticket me-2"></i>
                    Détail du ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span id="ticketDetailTypeMe" class="badge ticket-type-badge">Type</span>
                        <span id="ticketDetailPriorityMe" class="badge ticket-priority-badge">Priorité</span>
                    </div>
                    <span id="ticketDetailStatusMe" class="badge ticket-status-badge">Statut</span>
                </div>

                <h4 id="ticketDetailTitleMe" class="mb-2"></h4>
                <p id="ticketDetailDescriptionMe" class=" mb-3"></p>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <small class=" d-block">Créé par</small>
                        <span id="ticketDetailCreatorMe">—</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class=" d-block">Assigné à</small>
                        <span id="ticketDetailAssigneeMe">—</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class=" d-block">Échéance</small>
                        <span id="ticketDetailDueDateMe">—</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class=" d-block">Statut actuel</small>
                        <span id="ticketDetailStatusTextMe">—</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
