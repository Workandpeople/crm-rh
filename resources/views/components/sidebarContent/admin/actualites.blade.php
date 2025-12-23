@push('css')
<style>
  .admin-actualites{
    background: var(--color-bg);
    color: var(--color-text);
    min-height: calc(100vh - 4rem);
    padding: 2rem;
  }
  .admin-actualites .head{
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom: 1.25rem;
  }
  .admin-actualites .head h2{
    color: var(--color-primary);
    margin:0; font-size:1.6rem; font-weight:700;
  }
  .admin-actualites .head .subhead{
    color: var(--color-text-muted);
    margin:.25rem 0 0; font-size:.92rem;
    max-width: 820px;
  }
  .btn-add{
    background: linear-gradient(90deg, var(--color-primary), var(--color-primary-hover));
    color:#fff; border:0; border-radius:.6rem; font-weight:700;
    padding:.6rem 1.1rem; box-shadow:0 3px 10px rgba(79,70,229,.3);
    transition: all .25s ease;
  }
  .btn-add:hover{ transform: translateY(-2px); box-shadow:0 4px 15px rgba(79,70,229,.45); }
  .btn-ghost{
    background: transparent; color: var(--color-text);
    border:1px solid var(--color-border); border-radius:.6rem; font-weight:700;
    padding:.55rem 1rem; transition: all .2s ease;
  }
  .btn-ghost:hover{ border-color: var(--color-primary); color: var(--color-primary); }

  .filters{
    display:flex; flex-wrap:wrap; gap:1rem;
    margin-bottom: 1.25rem;
  }
  .filters .fg{
    flex:1 1 200px; display:flex; flex-direction:column; gap:.35rem;
  }
  .filters label{ font-weight:600; color: var(--color-text-muted); font-size:.9rem; }
  .input-icon{ position:relative; display:flex; align-items:center; }
  .input-icon i{ position:absolute; left:.85rem; color:var(--color-text-muted); pointer-events:none; }
  .input,.select{
    background: var(--color-bg); color: var(--color-text);
    border:1px solid var(--color-border); border-radius:.5rem; padding:.55rem .8rem; outline:0;
    transition: all .2s ease; width:100%;
  }
  .input{ padding-left:2.2rem; }
  .input:focus,.select:focus{ border-color: var(--color-primary); box-shadow:0 0 0 3px rgba(79,70,229,.25); }

  .stats{
    display:flex; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;
  }
  .stat-card{
    background: var(--color-bg-secondary);
    border:1px solid var(--color-border);
    border-radius:1rem; padding:1rem 1.25rem; min-width:180px;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
  }
  .stat-card .label{ color: var(--color-text-muted); text-transform:uppercase; font-size:.8rem; letter-spacing:.3px; }
  .stat-card .value{ font-weight:800; font-size:1.6rem; margin:0; }

  .tbl-wrap{
    background: var(--color-bg-secondary);
    border:1px solid var(--color-border);
    border-radius:1rem; overflow:hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,.15);
  }
  .tbl{
    width:100%; border-collapse: collapse; table-layout: fixed;
  }
  .tbl thead{
    background: rgba(255,255,255,.05);
    position: sticky; top: 0; z-index: 1;
  }
  .tbl th{
    padding: .9rem 1rem; text-align:left; border-bottom:2px solid var(--color-border);
    font-weight:700; font-size:.95rem; color: var(--color-text);
    text-transform: uppercase; letter-spacing:.35px;
  }
  .tbl td{
    padding: .9rem 1rem; border-bottom:1px solid var(--color-border); vertical-align: middle;
    color: var(--color-text);
  }
  .tbl tbody tr:hover{ background: rgba(255,255,255,.04); }
  .tbl td, .tbl th{ overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .col-actions{ width: 150px; }
  .badge-status{
    display:inline-block; padding:.3rem .65rem; border-radius:999px;
    font-weight:700; font-size:.85rem;
  }
  .badge-status.published{ background:#22C55E; color:#fff; }
  .badge-status.draft{ background:#F59E0B; color:#1E1E2F; }
  .badge-status.archived{ background:#9CA3AF; color:#111827; }

  .table-actions{
    display:flex; align-items:center; gap:.45rem; flex-wrap:wrap;
  }
  .btn-action{
    width:36px; height:36px; display:flex; align-items:center; justify-content:center;
    background: transparent; color: var(--color-text);
    border:1px solid var(--color-border); border-radius:.45rem;
    transition: all .2s ease; cursor:pointer;
  }
  .btn-action:hover{ transform: scale(1.05); }
  .btn-action.edit{ border-color: var(--color-primary); color: var(--color-primary); }
  .btn-action.edit:hover{ background: var(--color-primary); color:#fff; }
  .btn-action.delete{ border-color:#EF4444; color:#EF4444; }
  .btn-action.delete:hover{ background:#EF4444; color:#fff; }
  .btn-action.star{ border-color:#F59E0B; color:#F59E0B; }
  .btn-action.star.active{ background:#F59E0B; color:#1E1E2F; }

  .pagination-custom{
    display:flex; justify-content:center; gap:.75rem; padding:1rem 0;
  }
  .page-link-custom{
    border:1px solid var(--color-border); background: var(--color-bg-secondary);
    color: var(--color-text); border-radius:.5rem; padding:.55rem 1rem; min-width:110px;
  }
  .page-link-custom:disabled{ opacity:.5; cursor:not-allowed; }

  @media (max-width: 768px){
    .admin-actualites{ padding: 1rem; }
    .admin-actualites .head{ flex-direction:column; align-items:flex-start; gap:.6rem; }
    .btn-add, .btn-ghost{ width:100%; }
    .col-actions{ width:120px; }
  }
</style>
@endpush

<div class="admin-actualites" data-script="actualitesManagement">
  <div class="head">
    <div>
      <h2>Blog des actualités</h2>
      <p class="subhead">Filtrez, publiez et mettez en avant les articles destinés aux collaborateurs.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <button class="btn-ghost" id="btnRefresh"><i class="fa-solid fa-rotate-right me-2"></i> Rafraîchir</button>
      <button class="btn-add" id="btnCreate"><i class="fa-solid fa-plus me-2"></i> Nouvel article</button>
    </div>
  </div>

  <div class="stats">
    <div class="stat-card">
      <div class="label">Total</div>
      <p class="value" id="statTotal">—</p>
    </div>
    <div class="stat-card">
      <div class="label">Publiés</div>
      <p class="value text-success" id="statPublished">—</p>
    </div>
    <div class="stat-card">
      <div class="label">Brouillons</div>
      <p class="value text-warning" id="statDraft">—</p>
    </div>
    <div class="stat-card">
      <div class="label">Mises en avant</div>
      <p class="value text-primary" id="statHighlighted">—</p>
    </div>
  </div>

  <div class="filters">
    <div class="fg">
      <label for="filterSearch">Recherche</label>
      <div class="input-icon">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" class="input" id="filterSearch" placeholder="Titre ou auteur">
      </div>
    </div>
    <div class="fg">
      <label for="filterStatus">Statut</label>
      <select class="select" id="filterStatus">
        <option value="">Tous</option>
        <option value="published">Publié</option>
        <option value="draft">Brouillon</option>
        <option value="archived">Archivé</option>
      </select>
    </div>
    <div class="fg">
      <label for="filterHighlight">Mise en avant</label>
      <select class="select" id="filterHighlight">
        <option value="">Toutes</option>
        <option value="yes">Oui</option>
        <option value="no">Non</option>
      </select>
    </div>
    <div class="fg" style="align-self:flex-end; max-width:200px;">
      <button class="btn-ghost w-100" id="btnResetFilters">
        <i class="fa-solid fa-rotate-left me-2"></i> Réinitialiser
      </button>
    </div>
  </div>

  <div class="tbl-wrap">
    <table class="tbl" id="actualitesTable">
      <thead>
        <tr>
          <th class="text-start">Titre</th>
          <th>Auteur</th>
          <th>Statut</th>
          <th>Mise en avant</th>
          <th>Date</th>
          <th class="col-actions text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="7" class="text-center text-muted py-4">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
  <div id="paginationActualites" class="pagination-custom"></div>
</div>

{{-- Modal directement dans le DOM (non poussé) pour être chargé avec la vue --}}
<div class="modal fade" id="modalBlog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" id="blogForm">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBlogTitle">Nouvel article</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="blogId" name="blog_id">
        <input type="hidden" id="blogCompanyId" name="company_id">

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Titre *</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Statut *</label>
            <select name="status" class="form-select" required>
              <option value="draft">Brouillon</option>
              <option value="published">Publié</option>
              <option value="archived">Archivé</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Image principale</label>
            <input type="file" accept="image/*" name="main_image" class="form-control img-input" data-preview="#preview-main">
            <div class="mt-2">
              <img id="preview-main" class="img-fluid rounded d-none" style="max-height:140px;" alt="Preview image principale">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image</label>
            <input type="text" name="main_image_credit" class="form-control" placeholder="Crédit">
          </div>
          <div class="col-md-2 d-flex align-items-center">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="highlighted">
              <label class="form-check-label">Mise en avant</label>
            </div>
          </div>
        </div>

        <hr>
        <h6 class="fw-bold mb-2">Section 2</h6>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Sous-titre</label>
            <input type="text" name="second_title" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Type</label>
            <select name="second_type" class="form-select">
              <option value="horizontal">Horizontal</option>
              <option value="vertical">Vertical</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Image</label>
            <input type="file" accept="image/*" name="second_image" class="form-control img-input" data-preview="#preview-second">
            <div class="mt-2">
              <img id="preview-second" class="img-fluid rounded d-none" style="max-height:120px;" alt="Preview section 2">
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image</label>
            <input type="text" name="second_image_credit" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu</label>
            <textarea name="second_content" class="form-control" rows="3" placeholder="Texte de la section 2"></textarea>
          </div>
        </div>

        <h6 class="fw-bold mb-2">Section 3</h6>
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Type</label>
            <select name="third_type" class="form-select">
              <option value="horizontal">Horizontal</option>
              <option value="vertical">Vertical</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Image</label>
            <input type="file" accept="image/*" name="third_image" class="form-control img-input" data-preview="#preview-third">
            <div class="mt-2">
              <img id="preview-third" class="img-fluid rounded d-none" style="max-height:120px;" alt="Preview section 3">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Crédit image</label>
            <input type="text" name="third_image_credit" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu</label>
            <textarea name="third_content" class="form-control" rows="3" placeholder="Texte de la section 3"></textarea>
          </div>
        </div>

        <h6 class="fw-bold mb-2">Section 4</h6>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Type</label>
            <select name="fourth_type" class="form-select">
              <option value="horizontal">Horizontal</option>
              <option value="vertical">Vertical</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Image</label>
            <input type="file" accept="image/*" name="fourth_image" class="form-control img-input" data-preview="#preview-fourth">
            <div class="mt-2">
              <img id="preview-fourth" class="img-fluid rounded d-none" style="max-height:120px;" alt="Preview section 4">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Crédit image</label>
            <input type="text" name="fourth_image_credit" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu</label>
            <textarea name="fourth_content" class="form-control" rows="3" placeholder="Texte de la section 4"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

{{-- Confirmation suppression --}}
<div class="modal fade" id="modalDeleteBlog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Supprimer l’article</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Voulez-vous vraiment supprimer cet article ? Cette action est définitive.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBlog">Supprimer</button>
      </div>
    </div>
  </div>
</div>
