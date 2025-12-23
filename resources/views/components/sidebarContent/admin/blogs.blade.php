@push('css')
<style>
  .filter-row input,
  .filter-row select {
    border: 1px solid #d0d5dd;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 0.97rem;
  }
  table thead th {
    background-color: #f8fafc;
    font-weight: 600;
  }
  .section-divider {
    border-top: 2px dashed #e5e7eb;
    margin: 2rem 0;
  }
  .required-asterisk {
    color: #dc3545;
    font-weight: 700;
    margin-left: 0.2rem;
  }
  .card-hero {
    background: linear-gradient(135deg, #fef3c7 0%, #f9fafb 100%);
    border: 1px solid #fef08a;
    border-radius: 18px;
  }
</style>
@endpush

<div class="container-fluid py-5 px-4">

  {{-- === Hero / intro === --}}
  <div class="card card-hero mb-4 shadow-sm">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <p class="text-uppercase fw-bold text-muted mb-1" style="letter-spacing:2px;">Entreprise</p>
        <h1 class="fw-bold mb-2 fs-3">Gestion des articles</h1>
        <p class="mb-0 text-muted" style="max-width: 680px;">
          Centralisez vos colonnes et structurez chaque article en plusieurs sections texte + image.
        </p>
      </div>
      <button class="btn btn-primary fw-semibold" id="btnCreate">
        <i class="fa-solid fa-plus me-2"></i>Nouvel article
      </button>
    </div>
  </div>

  {{-- === Filtres === --}}
  <div class="filter-row row g-3 align-items-center mb-3">
    <div class="col-md-4">
      <input type="text" id="filterTitle" class="form-control" placeholder="Titre">
    </div>
    <div class="col-md-3">
      <input type="text" id="filterAuthor" class="form-control" placeholder="Auteur">
    </div>
    <div class="col-md-3">
      <select id="filterCategory" class="form-select">
        <option value="">Toutes les catégories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2 d-flex justify-content-md-end">
      <button class="btn btn-outline-secondary w-100" id="resetFilters">
        <i class="fa-solid fa-rotate-left me-2"></i>Réinitialiser
      </button>
    </div>
  </div>

  {{-- === Tableau === --}}
  <div class="table-responsive shadow-sm border rounded-3">
    <table class="table table-bordered align-middle text-center mb-0" id="blogsTable">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Auteur</th>
          <th>Catégorie</th>
          <th>Mise en avant</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="5" class="text-muted py-4">Chargement...</td></tr>
      </tbody>
    </table>
  </div>
  <div id="paginationBlogs" class="pagination-custom mt-3 text-center"></div>
</div>

@push('modals')
{{-- CREATE / EDIT MODAL --}}
<div class="modal fade" id="modalBlog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <form class="modal-content" method="POST" action="">
      @csrf
      <input type="hidden" name="_method" value="POST">
      <div class="modal-header">
        <h5 class="modal-title">Nouvel article</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- === SECTION 1 === --}}
        <h6 class="fw-bold mb-3">Informations principales</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Titre<span class="required-asterisk">*</span></label>
            <input type="text" name="title" class="form-control" placeholder="Titre" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Auteur<span class="required-asterisk">*</span></label>
            <input type="text" name="author" class="form-control" placeholder="Auteur" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Catégorie<span class="required-asterisk">*</span></label>
            <select name="category_id" class="form-select" required>
              <option value="">Choisir</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Image principale<span class="required-asterisk">*</span></label>
            <input type="file" name="main_image" class="form-control base64-image" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image <small class="text-muted">(optionnel)</small></label>
            <input type="text" name="main_image_credit" class="form-control" placeholder="Crédit image">
          </div>
          <div class="col-md-2 d-flex align-items-center">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="highlighted">
              <label class="form-check-label">Mise en avant</label>
            </div>
          </div>
        </div>

        <div class="section-divider"></div>

        {{-- === SECTION 2 === --}}
        <h6 class="fw-bold mb-3">Deuxième section</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Sous-titre<span class="required-asterisk">*</span></label>
            <input type="text" name="second_title" class="form-control" placeholder="Sous-titre" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Type<span class="required-asterisk">*</span></label>
            <select name="second_type" class="form-select" required>
              <option value="">Type</option>
              <option value="vertical">Vertical</option>
              <option value="horizontal">Horizontal</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Image deuxième section<span class="required-asterisk">*</span></label>
            <input type="file" name="second_image" class="form-control base64-image" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image <small class="text-muted">(optionnel)</small></label>
            <input type="text" name="second_image_credit" class="form-control" placeholder="Crédit image">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu<span class="required-asterisk">*</span></label>
            <textarea name="second_content" class="form-control" rows="3" placeholder="Contenu..." required></textarea>
          </div>
        </div>

        <div class="section-divider"></div>

        {{-- === SECTION 3 === --}}
        <h6 class="fw-bold mb-3">Troisième section</h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Image troisième section<span class="required-asterisk">*</span></label>
            <input type="file" name="third_image" class="form-control base64-image" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image <small class="text-muted">(optionnel)</small></label>
            <input type="text" name="third_image_credit" class="form-control" placeholder="Crédit image">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Type<span class="required-asterisk">*</span></label>
            <select name="third_type" class="form-select" required>
              <option value="">Type</option>
              <option value="vertical">Vertical</option>
              <option value="horizontal">Horizontal</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu<span class="required-asterisk">*</span></label>
            <textarea name="third_content" class="form-control" rows="3" placeholder="Contenu..." required></textarea>
          </div>
        </div>

        <div class="section-divider"></div>

        {{-- === SECTION 4 === --}}
        <h6 class="fw-bold mb-3">Quatrième section</h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Image quatrième section<span class="required-asterisk">*</span></label>
            <input type="file" name="fourth_image" class="form-control base64-image" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Crédit image <small class="text-muted">(optionnel)</small></label>
            <input type="text" name="fourth_image_credit" class="form-control" placeholder="Crédit image">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Type<span class="required-asterisk">*</span></label>
            <select name="fourth_type" class="form-select" required>
              <option value="">Type</option>
              <option value="vertical">Vertical</option>
              <option value="horizontal">Horizontal</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Contenu<span class="required-asterisk">*</span></label>
            <textarea name="fourth_content" class="form-control" rows="3" placeholder="Contenu..." required></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary fw-semibold">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

{{-- DELETE --}}
<div class="modal fade" id="modalDeleteConfirm" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title text-danger fw-bold">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Voulez-vous vraiment supprimer cet article ?</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</button>
      </div>
    </div>
  </div>
</div>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const tbody = document.querySelector('#blogsTable tbody');
  const paginationContainer = document.querySelector('#paginationBlogs');
  const modal = new bootstrap.Modal('#modalBlog');
  const form = document.querySelector('#modalBlog form');
  const submitBtn = form.querySelector('button[type="submit"]');
  const requiredFields = Array.from(form.querySelectorAll('[required]'));

  const fetchJSON = (url, options = {}) => fetch(url, options).then(r => r.json());

  function createToast(type, message) {
    const container = document.querySelector('.toast-container') || (() => {
      const div = document.createElement('div');
      div.className = 'toast-container position-fixed bottom-0 start-0 p-3 z-50';
      document.body.appendChild(div);
      return div;
    })();

    container.querySelectorAll('.toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show`;
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>`;
    container.appendChild(toast);
  }

  const isFieldFilled = (field) => {
    if (field.type === 'file') return field.files && field.files.length > 0;
    if (field.type === 'checkbox') return field.checked;
    return field.value && field.value.trim() !== '';
  };

  function updateSubmitState() {
    submitBtn.disabled = !requiredFields.every(isFieldFilled);
  }

  requiredFields.forEach(field => {
    const eventName = field.type === 'file' ? 'change' : 'input';
    field.addEventListener(eventName, updateSubmitState);
    if (field.tagName === 'SELECT') {
      field.addEventListener('change', updateSubmitState);
    }
  });

  updateSubmitState();

  async function loadBlogs(page = 1) {
    const title = document.querySelector('#filterTitle').value.trim();
    const author = document.querySelector('#filterAuthor').value.trim();
    const category = document.querySelector('#filterCategory').value;
    const params = new URLSearchParams({ page });
    if (title) params.append('search', title);
    if (author) params.append('search', author);
    if (category) params.append('category_id', category);

    const res = await fetchJSON(`/admin/columns/ajax?${params.toString()}`);
    renderTable(res.data);
    renderPagination(res.pagination);
  }

  function renderTable(data) {
    tbody.innerHTML = '';
    if (!data.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="text-muted py-4">Aucun article trouvé.</td></tr>`;
      return;
    }
    data.forEach(item => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="text-start">
          <div class="fw-bold">${item.title}</div>
          <small class="text-muted">${item.slug ?? ''}</small>
        </td>
        <td>${item.author}</td>
        <td>${item.category ? item.category.name : '-'}</td>
        <td>
          <button class="btn btn-sm ${item.highlighted ? 'btn-warning' : 'btn-outline-secondary'} toggle-highlight" data-id="${item.id}">
            ${item.highlighted ? '⭐' : '☆'}
          </button>
        </td>
        <td>
          <button class="btn btn-primary btn-sm me-2 edit-btn" data-id="${item.id}">
            <i class="fa-solid fa-pen"></i>
          </button>
          <button class="btn btn-danger btn-sm delete-btn" data-id="${item.id}">
            <i class="fa-solid fa-trash"></i>
          </button>
        </td>`;
      tbody.appendChild(row);
    });
    attachListeners();
  }

  function renderPagination(pagination) {
    paginationContainer.innerHTML = '';
    const totalPages = Math.ceil(pagination.total / pagination.per_page);
    const prev = `<button class="page-link-custom" ${pagination.current_page === 1 ? 'disabled' : ''}>
      <i class="fa-solid fa-arrow-left me-2"></i>Précédent
    </button>`;
    const next = `<button class="page-link-custom ms-2" ${pagination.current_page >= totalPages ? 'disabled' : ''}>
      Suivant<i class="fa-solid fa-arrow-right ms-2"></i>
    </button>`;
    paginationContainer.innerHTML = prev + next;
    paginationContainer.querySelectorAll('button')[0].onclick = () => loadBlogs(pagination.current_page - 1);
    paginationContainer.querySelectorAll('button')[1].onclick = () => loadBlogs(pagination.current_page + 1);
  }

  function attachListeners() {
    document.querySelectorAll('.edit-btn').forEach(btn =>
      btn.addEventListener('click', () => openModalBlog('edit', btn.dataset.id))
    );
    document.querySelectorAll('.delete-btn').forEach(btn =>
      btn.addEventListener('click', () => confirmDelete(btn.dataset.id))
    );
    document.querySelectorAll('.toggle-highlight').forEach(btn =>
      btn.addEventListener('click', () => toggleHighlight(btn.dataset.id))
    );
  }

  // === MODAL OPEN ===
  document.querySelector('#btnCreate').onclick = () => openModalBlog('create');

  async function openModalBlog(mode, id = null) {
    form.reset();
    form.querySelector('input[name="_method"]').value = mode === 'edit' ? 'PUT' : 'POST';
    form.action = mode === 'edit' ? `/admin/columns/${id}/update` : `/admin/columns/store`;
    document.querySelector('#modalBlog .modal-title').textContent = mode === 'edit'
      ? 'Modifier l’article'
      : 'Créer un article';

    document.querySelectorAll('.image-preview').forEach(p => p.remove());

    if (mode === 'edit') {
      try {
        const json = await fetchJSON(`/admin/columns/${id}/ajax`, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });

        if (!json.success || !json.data) {
          createToast('danger', 'Réponse serveur invalide.');
          return;
        }

        for (const [key, value] of Object.entries(json.data)) {
          const el = form.querySelector(`[name="${key}"]`);
          if (el) {
            if (el.type === 'checkbox') {
              el.checked = !!value;
            } else if (el.type !== 'file') {
              el.value = value ?? '';
            }
          }
        }

        ['main_image','second_image','third_image','fourth_image'].forEach(name => {
          const input = form.querySelector(`input[name="${name}"]`);
          if (json.data[name]) {
            const preview = document.createElement('img');
            preview.src = json.data[name].startsWith('images/')
              ? `/${json.data[name]}`
              : `/storage/${json.data[name]}`;
            preview.className = 'img-thumbnail mt-2 image-preview';
            preview.style.maxHeight = '120px';
            input.parentNode.appendChild(preview);
          }
        });
      } catch (error) {
        console.error('AJAX show error:', error);
        createToast('danger', 'Erreur réseau ou JSON invalide.');
      }
    }

    updateSubmitState();
    modal.show();
  }

  document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      const existing = e.target.parentNode.querySelector('.image-preview');
      if (existing) existing.remove();
      const img = document.createElement('img');
      img.className = 'img-thumbnail mt-2 image-preview';
      img.style.maxHeight = '120px';
      img.src = URL.createObjectURL(file);
      e.target.parentNode.appendChild(img);
    });
  });

  document.querySelector('#modalBlog form').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    const original = btn.innerHTML;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Envoi...`;

    try {
      const data = new FormData(form);
      const methodOverride = form.querySelector('input[name="_method"]').value || 'POST';
      const requestMethod = ['PUT', 'PATCH'].includes(methodOverride) ? 'POST' : methodOverride;
      const res = await fetch(form.action, {
        method: requestMethod,
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: data
      });
      const json = await res.json();

      if (json.success) {
        createToast('success', json.message || 'Article enregistré.');
        modal.hide();
        loadBlogs();
      } else {
        createToast('danger', json.error || 'Erreur lors de l’enregistrement.');
      }
    } catch {
      createToast('danger', 'Erreur réseau ou interne.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = original;
    }
  });

  let deleteId = null;
  function confirmDelete(id) {
    deleteId = id;
    new bootstrap.Modal('#modalDeleteConfirm').show();
  }
  document.querySelector('#confirmDeleteBtn').onclick = async () => {
    await fetch(`/admin/columns/${deleteId}/delete`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    createToast('success', 'Article supprimé.');
    loadBlogs();
    bootstrap.Modal.getInstance(document.querySelector('#modalDeleteConfirm')).hide();
  };

  async function toggleHighlight(id) {
    await fetch(`/admin/columns/${id}/highlight`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    loadBlogs();
  }

  ['filterTitle', 'filterAuthor', 'filterCategory'].forEach(id =>
    document.querySelector(`#${id}`).addEventListener('input', () => loadBlogs())
  );
  document.querySelector('#resetFilters').onclick = () => {
    document.querySelector('#filterTitle').value = '';
    document.querySelector('#filterAuthor').value = '';
    document.querySelector('#filterCategory').value = '';
    loadBlogs();
  };

  loadBlogs();
});
</script>
@endpush
