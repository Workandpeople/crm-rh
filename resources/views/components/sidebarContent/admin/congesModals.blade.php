{{-- MODALE DÉTAIL CONGÉ --}}
<div class="modal fade modal-dark" id="modalLeaveDetails" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header">
        <h5 class="modal-title fw-bold">
          <i class="fa-solid fa-umbrella-beach me-2"></i>
          Détail du congé / absence
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      {{-- BODY --}}
      <div class="modal-body">
        {{-- Badges type + statut --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <span id="leaveDetailType" class="badge leave-type-badge">Type</span>
          </div>
          <span id="leaveDetailStatus" class="badge leave-status-badge">Statut</span>
        </div>

        {{-- Employé + validateur --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Employé</small>
            <span id="leaveDetailEmployee">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Validé par</small>
            <span id="leaveDetailValidator">—</span>
          </div>
        </div>

        {{-- Période + durée --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Période</small>
            <span id="leaveDetailPeriod">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Durée</small>
            <span id="leaveDetailDuration">—</span>
          </div>
        </div>

        {{-- Justificatif --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Justificatif</small>
            <span id="leaveDetailJustification">—</span>
            {{-- ex: lien "Voir le justificatif" injecté en JS --}}
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Statut interne</small>
            <span id="leaveDetailInternalStatus">—</span>
          </div>
        </div>

        {{-- Commentaires --}}
        <div class="mt-3 border-top pt-3">
          <small class="text-muted d-block mb-1">Commentaires</small>
          <p id="leaveDetailComments" class="mb-0 text-muted" style="font-size:.9rem;">
            —
          </p>
        </div>

        {{-- Dates système --}}
        <div class="row mt-3">
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Créé le</small>
            <span id="leaveDetailCreatedAt">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class="text-muted d-block">Dernière mise à jour</small>
            <span id="leaveDetailUpdatedAt">—</span>
          </div>
        </div>
      </div>

      {{-- FOOTER --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Fermer
        </button>
      </div>

    </div>
  </div>
</div>
