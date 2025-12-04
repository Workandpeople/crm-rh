{{-- MODALE CRÉATION TICKET RH (multi-types) --}}
<div class="modal fade" id="modalTicketCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="formCreateTicket" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
            <i class="fa-solid fa-ticket"></i>
            Nouveau ticket RH
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          {{-- CHOIX TYPE DE TICKET --}}
          <div class="mb-3">
            <label class="form-label d-block mb-1">Type de ticket</label>
            <div class="d-flex flex-wrap gap-2 ticket-type-switcher">
              <button type="button"
                      class="btn btn-sm btn-outline-primary ticket-type-toggle active"
                      data-ticket-type="conge">
                <i class="fa-solid fa-plane-departure me-1"></i> Congé / absence
              </button>
              <button type="button"
                      class="btn btn-sm btn-outline-primary ticket-type-toggle"
                      data-ticket-type="note_frais">
                <i class="fa-solid fa-receipt me-1"></i> Note de frais
              </button>
              <button type="button"
                      class="btn btn-sm btn-outline-primary ticket-type-toggle"
                      data-ticket-type="document_rh">
                <i class="fa-solid fa-file-contract me-1"></i> Document RH
              </button>
              <button type="button"
                      class="btn btn-sm btn-outline-primary ticket-type-toggle"
                      data-ticket-type="incident">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Incident
              </button>
              <button type="button"
                      class="btn btn-sm btn-outline-secondary ticket-type-toggle"
                      data-ticket-type="autre">
                <i class="fa-solid fa-circle-question me-1"></i> Autre
              </button>
            </div>

            {{-- Valeur envoyée au back --}}
            <input type="hidden" name="type" id="ticketTypeInput" value="conge">
          </div>

          {{-- BLOC COMMUN (pour tous les types) --}}
          <div class="row mb-3">
            <div class="col-md-6 mb-3">
              <label class="form-label">Titre du ticket</label>
              <input type="text"
                     name="title"
                     id="ticketTitle"
                     class="form-control"
                     required
                     placeholder="Titre de la demande…">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Priorité</label>
              <select name="priority" id="ticketPriority" class="form-select">
                <option value="basse">Basse</option>
                <option value="moyenne" selected>Moyenne</option>
                <option value="haute">Haute</option>
              </select>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">Description</label>
              <textarea name="description"
                        id="ticketDescription"
                        class="form-control"
                        rows="3"
                        placeholder="Description de la demande…"></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Employé concerné</label>
              <select name="related_user_id" id="ticketRelatedUser" class="form-select">
                <option value="">— Sélectionner un employé —</option>
                {{-- options injectées en JS plus tard (users de la société) --}}
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Attribuer à</label>
              <select name="assignee_id" id="ticketAssignee" class="form-select">
                <option value="">— À définir plus tard —</option>
                {{-- options injectées en JS via /admin/backlogs/options --}}
              </select>
            </div>
          </div>

          {{-- ====== BLOCS SPÉCIFIQUES PAR TYPE ===================== --}}

          {{-- CONGÉ / ABSENCE --}}
          <div class="ticket-extra-group" data-ticket-type="conge">
            <h6 class="mb-2 fw-semibold">Informations congé</h6>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Type de congé</label>
                <select name="leave_type" id="ticketLeaveType" class="form-select">
                  <option value="CP">Congés payés</option>
                  <option value="SansSolde">Sans solde</option>
                  <option value="Exceptionnel">Absence exceptionnelle</option>
                  <option value="Maladie">Maladie</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Début</label>
                <input type="date" name="leave_start_date" id="ticketLeaveStart" class="form-control">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Fin</label>
                <input type="date" name="leave_end_date" id="ticketLeaveEnd" class="form-control">
              </div>
            </div>
          </div>

          {{-- NOTE DE FRAIS --}}
          <div class="ticket-extra-group d-none" data-ticket-type="note_frais">
            <h6 class="mb-2 fw-semibold">Informations note de frais</h6>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Type de dépense</label>
                <select name="expense_type" id="ticketExpenseType" class="form-select">
                  <option value="repas">Repas</option>
                  <option value="peage">Péage / autoroute</option>
                  <option value="hebergement">Hébergement</option>
                  <option value="km">Kilométrage</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Montant (€)</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       name="expense_amount"
                       id="ticketExpenseAmount"
                       class="form-control"
                       placeholder="Ex : 23,90">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Date de la dépense</label>
                <input type="date"
                       name="expense_date"
                       id="ticketExpenseDate"
                       class="form-control">
              </div>
              {{-- V2 : justificatif (upload) --}}
              {{--
              <div class="col-12 mb-2">
                <label class="form-label">Justificatif (PDF / image)</label>
                <input type="file" name="expense_receipt" class="form-control">
              </div>
              --}}
            </div>
          </div>

          {{-- DOCUMENT RH --}}
          <div class="ticket-extra-group d-none" data-ticket-type="document_rh">
            <h6 class="mb-2 fw-semibold">Informations document RH</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Type de document</label>
                <select name="document_type" id="ticketDocumentType" class="form-select">
                  <option value="CNI">Carte d’identité</option>
                  <option value="Carte Vitale">Carte Vitale</option>
                  <option value="Permis">Permis de conduire</option>
                  <option value="Contrat">Contrat de travail</option>
                  <option value="Fiche Fonction">Fiche de fonction</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Date d’expiration (facultatif)</label>
                <input type="date"
                       name="document_expires_at"
                       id="ticketDocumentExpiresAt"
                       class="form-control">
              </div>
            </div>
          </div>

          {{-- INCIDENT --}}
          <div class="ticket-extra-group d-none" data-ticket-type="incident">
            <h6 class="mb-2 fw-semibold">Informations incident</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Gravité</label>
                <select name="incident_severity" id="ticketIncidentSeverity" class="form-select">
                  <option value="mineur">Mineur</option>
                  <option value="majeur">Majeur</option>
                  <option value="critique">Critique</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Échéance de traitement</label>
                <input type="date" name="due_date" id="ticketDueDate" class="form-control">
              </div>
            </div>
          </div>

          {{-- AUTRE --}}
          <div class="ticket-extra-group d-none" data-ticket-type="autre">
            <h6 class="mb-2 fw-semibold">Informations complémentaires</h6>
            <p class="text-muted mb-2" style="font-size:.9rem;">
              Utilisez ce ticket pour toute demande ne rentrant pas dans les autres catégories
              (question RH, suivi, demande diverse, etc.).
            </p>
          </div>

        </div>{{-- /modal-body --}}

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Annuler
          </button>
          <button type="submit" class="btn btn-primary">
            Créer le ticket
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
