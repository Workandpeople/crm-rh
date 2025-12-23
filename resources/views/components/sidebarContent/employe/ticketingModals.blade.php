{{-- resources/views/employe/ticketingModals.blade.php --}}
@php $user = auth()->user(); @endphp

{{-- MODALE CRÉATION --}}
<div class="modal fade" id="modalEmployeeTicketCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="formEmployeeCreateTicket" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
            <i class="fa-solid fa-ticket"></i> Nouveau ticket
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          {{-- company_id côté employé --}}
          <input type="hidden" name="company_id" value="{{ $user->company_id }}">
          <input type="hidden" name="type" id="employeeTicketTypeInput" value="conge">

          <div class="mb-3">
            <label class="form-label d-block mb-1">Type de ticket</label>
            <div class="d-flex flex-wrap gap-2 ticket-type-switcher">
              <button type="button" class="btn btn-sm btn-outline-primary ticket-type-toggle active" data-ticket-type="conge">
                <i class="fa-solid fa-plane-departure me-1"></i> Congé
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary ticket-type-toggle" data-ticket-type="note_frais">
                <i class="fa-solid fa-receipt me-1"></i> Note de frais
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary ticket-type-toggle" data-ticket-type="document_rh">
                <i class="fa-solid fa-file-contract me-1"></i> Document RH
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary ticket-type-toggle" data-ticket-type="incident">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Incident
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary ticket-type-toggle" data-ticket-type="autre">
                <i class="fa-solid fa-circle-question me-1"></i> Autre
              </button>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8 mb-3">
              <label class="form-label">Titre</label>
              <input type="text" name="title" id="employeeTicketTitle" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Priorité</label>
              <select name="priority" id="employeeTicketPriority" class="form-select">
                <option value="basse">Basse</option>
                <option value="moyenne" selected>Moyenne</option>
                <option value="haute">Haute</option>
              </select>
            </div>
            <div class="col-12 mb-2">
              <label class="form-label">Description</label>
              <textarea name="description" id="employeeTicketDescription" class="form-control" rows="3"></textarea>
            </div>
          </div>

          {{-- CONGÉ --}}
          <div class="ticket-extra-group" data-ticket-type="conge">
            <h6 class="mb-2 fw-semibold">Infos congé</h6>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Type</label>
                <select name="leave_type" class="form-select">
                  <option value="CP">Congés payés</option>
                  <option value="SansSolde">Sans solde</option>
                  <option value="Exceptionnel">Absence exceptionnelle</option>
                  <option value="Maladie">Maladie</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Début</label>
                <input type="date" name="leave_start_date" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Fin</label>
                <input type="date" name="leave_end_date" class="form-control">
              </div>
            </div>
          </div>

          {{-- NOTE DE FRAIS --}}
          <div class="ticket-extra-group d-none" data-ticket-type="note_frais">
            <h6 class="mb-2 fw-semibold">Infos note de frais</h6>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Type</label>
                <select name="expense_type" class="form-select">
                  <option value="repas">Repas</option>
                  <option value="peage">Péage</option>
                  <option value="hebergement">Hébergement</option>
                  <option value="km">Kilométrage</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Montant (€)</label>
                <input type="number" step="0.01" min="0" name="expense_amount" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="expense_date" class="form-control">
              </div>
            </div>
          </div>

          {{-- DOCUMENT RH --}}
          <div class="ticket-extra-group d-none" data-ticket-type="document_rh">
            <h6 class="mb-2 fw-semibold">Infos document RH</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Type</label>
                <select name="document_type" class="form-select">
                  <option value="CNI">Carte d’identité</option>
                  <option value="Carte Vitale">Carte Vitale</option>
                  <option value="Permis">Permis</option>
                  <option value="Contrat">Contrat</option>
                  <option value="Fiche Fonction">Fiche de fonction</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Expiration (facultatif)</label>
                <input type="date" name="document_expires_at" class="form-control">
              </div>
            </div>
          </div>

          {{-- INCIDENT --}}
          <div class="ticket-extra-group d-none" data-ticket-type="incident">
            <h6 class="mb-2 fw-semibold">Infos incident</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Gravité</label>
                <select name="incident_severity" class="form-select">
                  <option value="mineur">Mineur</option>
                  <option value="majeur">Majeur</option>
                  <option value="critique">Critique</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Échéance</label>
                <input type="date" name="due_date" class="form-control">
              </div>
            </div>
          </div>

          {{-- AUTRE --}}
          <div class="ticket-extra-group d-none" data-ticket-type="autre">
            <h6 class="mb-2 fw-semibold">Infos complémentaires</h6>
            <p class="text-muted mb-0" style="font-size:.9rem;">
              Décris ta demande dans le titre et la description.
            </p>
          </div>

          <div class="alert alert-danger d-none mt-3" id="employeeCreateError"></div>
          <div class="alert alert-success d-none mt-3" id="employeeCreateSuccess"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">
            <span class="me-2 d-none" id="employeeCreateSpinner"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
            Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODALE DÉTAIL --}}
<div class="modal fade" id="modalEmployeeTicketDetails" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-info"></i> Détail du ticket
          </h5>
          <p class="mb-0 text-muted" style="font-size:.85rem;">
            Vos informations et l’état de traitement.
          </p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <span id="empDetailType" class="badge ticket-type-badge"></span>
            <span id="empDetailStatus" class="badge ticket-status-badge"></span>
            <span id="empDetailPriority" class="badge ticket-priority-badge"></span>
          </div>
          <small class="text-muted">
            Créé le <span id="empDetailCreatedAt">—</span>
          </small>
        </div>

        <div class="mb-3">
          <h5 id="empDetailTitle" class="mb-1">—</h5>
          <p id="empDetailDescription" class="mb-0 text-muted" style="white-space:pre-line;">—</p>
        </div>

        <hr>

        {{-- extra conge --}}
        <div class="ticket-details-extra" data-ticket-type="conge">
          <h6 class="fw-semibold mb-2"><i class="fa-solid fa-plane-departure me-1"></i> Congé</h6>
          <div class="row">
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Type</span><span id="empLeaveType">—</span></div>
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Début</span><span id="empLeaveStart">—</span></div>
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Fin</span><span id="empLeaveEnd">—</span></div>
          </div>
        </div>

        <div class="ticket-details-extra d-none" data-ticket-type="note_frais">
          <h6 class="fw-semibold mb-2"><i class="fa-solid fa-receipt me-1"></i> Note de frais</h6>
          <div class="row">
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Type</span><span id="empExpenseType">—</span></div>
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Montant</span><span id="empExpenseAmount">—</span></div>
            <div class="col-md-4 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Date</span><span id="empExpenseDate">—</span></div>
          </div>
        </div>

        <div class="ticket-details-extra d-none" data-ticket-type="document_rh">
          <h6 class="fw-semibold mb-2"><i class="fa-solid fa-file-contract me-1"></i> Document RH</h6>
          <div class="row">
            <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Type</span><span id="empDocType">—</span></div>
            <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Expiration</span><span id="empDocExp">—</span></div>
          </div>
        </div>

        <div class="ticket-details-extra d-none" data-ticket-type="incident">
          <h6 class="fw-semibold mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Incident</h6>
          <div class="row">
            <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Gravité</span><span id="empIncidentSeverity">—</span></div>
            <div class="col-md-6 mb-2"><span class="text-muted d-block" style="font-size:.8rem;">Échéance</span><span id="empIncidentDueDate">—</span></div>
          </div>
        </div>

        <div class="ticket-details-extra d-none" data-ticket-type="autre">
          <h6 class="fw-semibold mb-2"><i class="fa-solid fa-circle-question me-1"></i> Autre</h6>
          <p class="text-muted mb-0" style="font-size:.9rem;">
            Les informations sont dans le titre et la description.
          </p>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
