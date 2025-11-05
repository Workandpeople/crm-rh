<!-- CREATE -->
<div class="modal fade" id="modalCompanyCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formCreateCompany" enctype="multipart/form-data">@csrf
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Créer une société</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Domaine (ex: @workandpeople.fr)</label>
            <input type="text" name="domain" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="phone" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" name="address" class="form-control">
          </div>

          {{-- Drag & drop logo --}}
          <div class="mb-3">
            <label class="form-label d-block">Logo</label>
            <div id="createLogoDrop" class="border rounded p-3 text-center" style="cursor:pointer;">
              <div class="mb-2">
                <img id="createLogoPreview" src="{{ asset('images/placeholder-logo.png') }}" alt="Preview" style="max-height:80px;">
              </div>
              <p class="mb-1 ">Glissez-déposez une image, ou cliquez pour parcourir</p>
              <small class="">PNG, JPG, JPEG, WEBP. Max 2 Mo</small>
              <input id="createLogoFile" name="logo" type="file" accept="image/*" class="d-none">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Administrateur</label>
            <select name="admin_user_id" id="createCompanyAdminSelect" class="form-select">
              <option value="">Aucun</option>
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

<!-- EDIT -->
<div class="modal fade" id="modalCompanyEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditCompany" enctype="multipart/form-data">@csrf @method('PUT')
        <input type="hidden" id="editCompanyId" name="id">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Modifier la société</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nom</label>
            <input type="text" id="editName" name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Domaine</label>
            <input type="text" id="editDomain" name="domain" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label>
            <input type="email" id="editEmail" name="email" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Téléphone</label>
            <input type="text" id="editPhone" name="phone" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Adresse</label>
            <input type="text" id="editAddress" name="address" class="form-control"></div>

          {{-- Drag & drop logo + preview actuel --}}
          <div class="mb-3">
            <label class="form-label d-block">Logo</label>
            <div id="editLogoDrop" class="border rounded p-3 text-center" style="cursor:pointer;">
              <div class="mb-2">
                <img id="editLogoPreview" src="{{ asset('images/placeholder-logo.png') }}" alt="Preview" style="max-height:80px;">
              </div>
              <p class="mb-1 ">Glissez-déposez une image, ou cliquez pour parcourir</p>
              <small class="">PNG, JPG, JPEG, WEBP. Max 2 Mo</small>
              <input id="editLogoFile" name="logo" type="file" accept="image/*" class="d-none">
            </div>
            <small class=" d-block mt-2">Logo actuel : <span id="editLogoCurrentPath">—</span></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Administrateur</label>
            <select name="admin_user_id" id="editCompanyAdminSelect" class="form-select">
              <option value="">Aucun</option>
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

<!-- DELETE -->
<div class="modal fade" id="modalCompanyDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-danger">Supprimer une société</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <p>Confirmer la suppression ? Cette action est réversible (corbeille).</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="btnConfirmDeleteCompany" class="btn btn-danger">Supprimer</button>
      </div>
    </div>
  </div>
</div>
