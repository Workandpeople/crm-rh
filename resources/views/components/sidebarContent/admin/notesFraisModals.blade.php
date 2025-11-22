{{-- MODALE DÉTAIL NOTE DE FRAIS --}}
<div class="modal fade modal-dark" id="modalExpenseDetails" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">
          <i class="fa-solid fa-receipt me-2"></i>
          Détail de la note de frais
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body">
        {{-- En-tête : type + statut --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <span id="expenseDetailType" class="badge expense-type-badge">Type</span>
          <span id="expenseDetailStatus" class="badge expense-status-badge">Statut</span>
        </div>

        {{-- Montant + date --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class=" d-block">Montant</small>
            <span id="expenseDetailAmount">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class=" d-block">Date</small>
            <span id="expenseDetailDate">—</span>
          </div>
        </div>

        {{-- Employé + société --}}
        <div class="row mb-3">
          <div class="col-md-6 mb-2">
            <small class=" d-block">Employé</small>
            <span id="expenseDetailUser">—</span>
          </div>
          <div class="col-md-6 mb-2">
            <small class=" d-block">Société</small>
            <span id="expenseDetailCompany">—</span>
          </div>
        </div>

        {{-- Description --}}
        <div class="mb-3">
          <small class=" d-block">Description</small>
          <p id="expenseDetailDescription" class="mb-0 " style="font-size:.9rem;">
            —
          </p>
        </div>

        {{-- Justificatif --}}
        <div class="mb-2">
          <small class=" d-block">Justificatif</small>
          <span id="expenseDetailReceipt">—</span>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Fermer
        </button>
      </div>

    </div>
  </div>
</div>
