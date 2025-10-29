<!-- === MODAL CRÉATION === -->
<div class="modal fade" id="modalUserCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formCreateUser">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Créer un utilisateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role_id" class="form-select" required id="createRoleSelect"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Société</label>
            <select name="company_id" class="form-select" id="createCompanySelect">
              <option value="">Aucune</option>
            </select>
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

<!-- === MODAL ÉDITION === -->
<div class="modal fade" id="modalUserEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditUser">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="editUserId">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Modifier l’utilisateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="first_name" class="form-control" id="editFirstName" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="last_name" class="form-control" id="editLastName" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="editEmail" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role_id" id="editRoleSelect" class="form-select" required></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Société</label>
            <select name="company_id" id="editCompanySelect" class="form-select">
              <option value="">Aucune</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select name="status" id="editStatusSelect" class="form-select">
              <option value="active">Actif</option>
              <option value="inactive">Inactif</option>
              <option value="pending">En attente</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- === MODAL SUPPRESSION === -->
<div class="modal fade" id="modalUserDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-danger">Supprimer un utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="btnConfirmDelete" class="btn btn-danger">Supprimer</button>
      </div>
    </div>
  </div>
</div>
