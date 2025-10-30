<!-- CREATE -->
<div class="modal fade" id="modalTeamCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formCreateTeam">@csrf
        <div class="modal-header"><h5 class="fw-bold">Créer une équipe</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Société</label><select id="createTeamCompanySelect" name="company_id" class="form-select" required></select></div>
          <div class="mb-3"><label class="form-label">Chef d’équipe</label><select id="createTeamLeaderSelect" name="leader_user_id" class="form-select"></select></div>
          <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Créer</button></div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT -->
<div class="modal fade" id="modalTeamEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditTeam">@csrf @method('PUT')
        <input type="hidden" id="editTeamId" name="id">
        <div class="modal-header"><h5 class="fw-bold">Modifier l’équipe</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nom</label><input id="editTeamName" type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Société</label><select id="editTeamCompanySelect" name="company_id" class="form-select" required></select></div>
          <div class="mb-3"><label class="form-label">Chef d’équipe</label><select id="editTeamLeaderSelect" name="leader_user_id" class="form-select"></select></div>
          <div class="mb-3"><label class="form-label">Description</label><textarea id="editTeamDesc" name="description" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success">Mettre à jour</button></div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE -->
<div class="modal fade" id="modalTeamDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="text-danger fw-bold">Supprimer une équipe</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><p>Confirmer la suppression ? Cette action est réversible (corbeille).</p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="button" id="btnConfirmDeleteTeam" class="btn btn-danger">Supprimer</button></div>
    </div>
  </div>
</div>
