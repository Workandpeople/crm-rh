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

{{-- MODALE VUE ARTICLE --}}
<div class="modal fade" id="modalBlogView" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content blog-view-modal">
      <div class="modal-header">
        <div class="d-flex flex-column gap-1">
          <h5 class="modal-title" id="blogViewTitle">—</h5>

          <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="badge blog-badge" id="blogViewStatus">—</span>
            <span class="badge blog-badge" id="blogViewHighlight">—</span>
            <span class="text-muted" style="font-size:.9rem;">
              <span id="blogViewAuthor">—</span> • <span id="blogViewDate">—</span>
            </span>
          </div>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        {{-- HERO --}}
        <div class="blog-view-hero">
          <div class="blog-view-hero-img">
            <img id="blogViewMainImage" class="d-none" alt="Image principale">
            <div class="blog-view-hero-placeholder" id="blogViewMainPlaceholder">
              <i class="fa-regular fa-image"></i>
              <span>Aucune image</span>
            </div>
          </div>

          <div class="blog-view-hero-meta">
            <div class="blog-view-field">
              <div class="k">Crédit image</div>
              <div class="v" id="blogViewMainCredit">—</div>
            </div>
            <div class="blog-view-field">
              <div class="k">Société</div>
              <div class="v" id="blogViewCompany">—</div>
            </div>
            <div class="blog-view-field">
              <div class="k">ID</div>
              <div class="v" id="blogViewId">—</div>
            </div>

            <div class="blog-view-actions">
              <button type="button" class="btn btn-primary" id="btnEditFromView">
                <i class="fa-solid fa-pen me-2"></i> Modifier
              </button>
            </div>
          </div>
        </div>

        {{-- SECTIONS --}}
        <div class="blog-view-sections">

          {{-- Section 2 --}}
          <div class="blog-section-card d-none" id="blogViewSection2">
            <div class="blog-section-head">
              <h6 class="m-0">Section 2</h6>
              <span class="pill" id="blogViewSection2Type">—</span>
            </div>

            <div class="blog-section-layout" id="blogViewSection2Layout">
              <div class="blog-section-media">
                <img id="blogViewSecondImage" class="d-none" alt="Image section 2">
                <div class="media-placeholder" id="blogViewSecondPlaceholder">
                  <i class="fa-regular fa-image"></i>
                  <span>Aucune image</span>
                </div>
                <div class="media-credit text-muted" id="blogViewSecondCredit"></div>
              </div>

              <div class="blog-section-content">
                <h5 class="section-title" id="blogViewSecondTitle"></h5>
                <p class="section-text" id="blogViewSecondContent"></p>
              </div>
            </div>
          </div>

          {{-- Section 3 --}}
          <div class="blog-section-card d-none" id="blogViewSection3">
            <div class="blog-section-head">
              <h6 class="m-0">Section 3</h6>
              <span class="pill" id="blogViewSection3Type">—</span>
            </div>

            <div class="blog-section-layout" id="blogViewSection3Layout">
              <div class="blog-section-media">
                <img id="blogViewThirdImage" class="d-none" alt="Image section 3">
                <div class="media-placeholder" id="blogViewThirdPlaceholder">
                  <i class="fa-regular fa-image"></i>
                  <span>Aucune image</span>
                </div>
                <div class="media-credit text-muted" id="blogViewThirdCredit"></div>
              </div>

              <div class="blog-section-content">
                <p class="section-text" id="blogViewThirdContent"></p>
              </div>
            </div>
          </div>

          {{-- Section 4 --}}
          <div class="blog-section-card d-none" id="blogViewSection4">
            <div class="blog-section-head">
              <h6 class="m-0">Section 4</h6>
              <span class="pill" id="blogViewSection4Type">—</span>
            </div>

            <div class="blog-section-layout" id="blogViewSection4Layout">
              <div class="blog-section-media">
                <img id="blogViewFourthImage" class="d-none" alt="Image section 4">
                <div class="media-placeholder" id="blogViewFourthPlaceholder">
                  <i class="fa-regular fa-image"></i>
                  <span>Aucune image</span>
                </div>
                <div class="media-credit text-muted" id="blogViewFourthCredit"></div>
              </div>

              <div class="blog-section-content">
                <p class="section-text" id="blogViewFourthContent"></p>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
