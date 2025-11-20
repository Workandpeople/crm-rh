{{-- MODALE CRÉATION TICKET --}}
<div class="modal fade" id="modalTicketCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formCreateTicket">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Nouveau ticket RH</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          {{-- Type --}}
          <div class="mb-3">
            <label class="form-label">Type de demande</label>
            <select name="type" id="ticketType" class="form-select" required>
              <option value="conge">Congé / absence</option>
              <option value="note_frais">Note de frais</option>
              <option value="incident">Incident</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          {{-- Titre --}}
          <div class="mb-3">
              <label class="form-label">Titre</label>
              <input type="text" name="title" id="ticketTitle" class="form-control" required
              placeholder="Ex : Demande de congé du 12 au 18 mars">
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="ticketDescription" class="form-control" rows="4"
                placeholder="Précisez le contexte, les dates, les détails nécessaires…"></textarea>
            </div>

            {{-- Priorité --}}
            <div class="mb-3">
            <label class="form-label">Priorité</label>
            <select name="priority" id="ticketPriority" class="form-select">
                <option value="basse">Basse</option>
                <option value="moyenne" selected>Moyenne</option>
                <option value="haute">Haute</option>
            </select>
            </div>

          {{-- Assignation --}}
          <div class="mb-3">
            <label class="form-label">Attribuer à</label>
            <select name="assignee_id" id="ticketAssignee" class="form-select">
              <option value="">— À définir plus tard —</option>
              {{-- options injectées en JS via /admin/backlogs/options --}}
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Créer le ticket</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODALE DÉTAIL TICKET --}}
<div class="modal fade modal-dark" id="modalTicketDetails" tabindex="-1" aria-hidden="true">
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
        {{-- En-tête : type + statut + priorité --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <span id="ticketDetailType" class="badge ticket-type-badge">Type</span>
            <span id="ticketDetailPriority" class="badge ticket-priority-badge">Priorité</span>
          </div>
          <span id="ticketDetailStatus" class="badge ticket-status-badge">Statut</span>
        </div>

        {{-- Titre --}}
        <h4 id="ticketDetailTitle" class="mb-2"></h4>
        <p id="ticketDetailDescription" class="text-muted mb-3"></p>

        {{-- Métadonnées principales --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Créé par</small>
            <span id="ticketDetailCreator">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Assigné à</small>
            <span id="ticketDetailAssignee">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Employé concerné</small>
            <span id="ticketDetailRelatedUser">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Société</small>
            <span id="ticketDetailCompany">—</span>
          </div>
        </div>

        {{-- Dates --}}
        <div class="row">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Créé le</small>
            <span id="ticketDetailCreatedAt">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Échéance</small>
            <span id="ticketDetailDueDate">—</span>
          </div>
        </div>

        {{-- Zone commentaires / historique (placeholder pour plus tard) --}}
        <div class="mt-3 border-top pt-3">
          <small class="text-muted d-block mb-1">Commentaires (à venir)</small>
          <p class="mb-0 text-muted" style="font-size: .85rem;">
            L’historique des échanges et commentaires sera affiché ici dans une prochaine étape.
          </p>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
          Fermer
        </button>
      </div>

    </div>
  </div>
</div>
