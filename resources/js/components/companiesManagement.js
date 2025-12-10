/**
 * resources/js/components/companiesManagement.js
 * Gestion des sociétés (Super Admin)
 * - Filtres admin + recherche
 * - Pagination client (15/page)
 * - Drag & drop logo + preview
 * - Modals CRUD
 * - Toasts Bootstrap
 */
export default function initCompaniesManagement() {
  const bs = window.bootstrap;
  if (!bs?.Modal || !bs?.Toast) return;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // UI
  const listEl       = document.getElementById('companiesList');
  const adminFilter  = document.getElementById('filter-admin');
  const searchInput  = document.getElementById('filter-search');
  const paginationEl = document.getElementById('companiesPagination');

  const modalCreate  = document.getElementById('modalCompanyCreate') ? new bs.Modal('#modalCompanyCreate') : null;
  const modalEdit    = document.getElementById('modalCompanyEdit')   ? new bs.Modal('#modalCompanyEdit')   : null;
  const modalDelete  = document.getElementById('modalCompanyDelete') ? new bs.Modal('#modalCompanyDelete') : null;

  // Drop areas + previews
  const createLogoDrop = document.getElementById('createLogoDrop');
  const createLogoFile = document.getElementById('createLogoFile');
  const createLogoPrev = document.getElementById('createLogoPreview');

  const editLogoDrop   = document.getElementById('editLogoDrop');
  const editLogoFile   = document.getElementById('editLogoFile');
  const editLogoPrev   = document.getElementById('editLogoPreview');
  const editLogoCurrent= document.getElementById('editLogoCurrentPath');

  // Toast helper
  function showToast(msg, type='success') {
    const container = document.getElementById('toastContainer') || (() => {
      const c = document.createElement('div');
      c.id = 'toastContainer';
      c.className = 'toast-container position-fixed top-0 end-0 p-3';
      document.body.appendChild(c);
      return c;
    })();

    const wrap = document.createElement('div');
    wrap.innerHTML = `
      <div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">${msg}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>`;
    const toastEl = wrap.firstElementChild;
    container.appendChild(toastEl);
    new bs.Toast(toastEl, { delay: 3000 }).show();
  }

  // Drag & drop binder
  function bindDropArea(areaEl, fileInputEl, previewImgEl) {
    if (!areaEl || !fileInputEl || !previewImgEl) return;

    const openPicker = () => fileInputEl.click();

    const setPreview = file => {
      if (!file) return;
      const url = URL.createObjectURL(file);
      previewImgEl.src = url;
    };

    areaEl.addEventListener('click', openPicker);

    fileInputEl.addEventListener('change', () => {
      const f = fileInputEl.files?.[0];
      setPreview(f);
    });

    ['dragenter','dragover'].forEach(evt =>
      areaEl.addEventListener(evt, e => {
        e.preventDefault(); e.stopPropagation();
        areaEl.classList.add('border-primary');
      })
    );
    ['dragleave','dragend','drop'].forEach(evt =>
      areaEl.addEventListener(evt, e => {
        e.preventDefault(); e.stopPropagation();
        areaEl.classList.remove('border-primary');
      })
    );

    areaEl.addEventListener('drop', e => {
      const file = e.dataTransfer.files?.[0];
      if (file) {
        fileInputEl.files = e.dataTransfer.files;
        setPreview(file);
      }
    });
  }

  // Bind initial create drop
  bindDropArea(createLogoDrop, createLogoFile, createLogoPrev);

  // Data
  let adminsCache = [];
  let companiesCache = [];
  let currentPage = 1;
  const perPage = 15;
  let toDeleteId = null;

  // Options
  async function fetchOptions() {
    try {
      const res = await fetch('/admin/companies/options', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('HTTP '+res.status);
      const data = await res.json();
      adminsCache = data.admins || [];

      // filtre admin
      if (adminFilter) {
        adminFilter.innerHTML = `<option value="">Tous</option>` +
          adminsCache.map(a => `<option value="${a.id}">${a.last_name.toUpperCase()} ${a.first_name}</option>`).join('');
      }

      // selects modals
      const selects = document.querySelectorAll('#createCompanyAdminSelect, #editCompanyAdminSelect');
      const html = `<option value="">Aucun</option>` +
        adminsCache.map(a => `<option value="${a.id}">${a.last_name.toUpperCase()} ${a.first_name} — ${a.email}</option>`).join('');
      selects.forEach(s => s.innerHTML = html);
    } catch (e) {
      showToast('Erreur chargement options', 'danger');
    }
  }

  // Load list
  async function loadCompanies() {
    if (listEl) listEl.innerHTML = '';
    try {
      const res = await fetch('/admin/companies', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('HTTP '+res.status);
      companiesCache = await res.json();
      render();
    } catch (e) {
      if (listEl) listEl.innerHTML = `<p class="text-danger">Erreur de chargement</p>`;
    }
  }

  // Filters + pagination
  function render() {
    if (!listEl) return;
    const adminId = adminFilter?.value || '';
    const q = (searchInput?.value || '').trim().toLowerCase();

    const filtered = companiesCache.filter(c => {
      const matchAdmin = !adminId || c.admin_user_id === adminId;
      const text = `${c.name} ${c.domain}`.toLowerCase();
      const matchSearch = !q || text.includes(q);
      return matchAdmin && matchSearch;
    });

    const totalPages = Math.ceil(filtered.length / perPage);
    if (currentPage > Math.max(totalPages, 1)) currentPage = 1;

    const start = (currentPage - 1) * perPage;
    const pageData = filtered.slice(start, start + perPage);

    listEl.innerHTML = pageData.length ? pageData.map(cardHTML).join('') :
      `<div class="">Aucune société trouvée</div>`;

    renderPagination(totalPages);
    bindRowActions();
  }

  function cardHTML(c) {
    const adminName = c.admin?.first_name ? `${c.admin.first_name} ${c.admin.last_name}` : '-';
    const logo = c.logo_path
      ? (c.logo_path.startsWith('http') ? c.logo_path : `/${c.logo_path}`)
      : '/images/placeholder_logo.jpg';
    return `
      <div class="societe-card" data-id="${c.id}">
        <div class="societe-header d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-3">
            <img src="${logo}" alt="Logo ${c.name}" class="societe-logo">
            <div>
              <h4 class="mb-1">${c.name}</h4>
              <p class=" mb-0">${c.domain}</p>
            </div>
          </div>
          <span class="status active">Active</span>
        </div>

        <div class="societe-info">
          <p><i class="fa-solid fa-envelope me-2 text-primary"></i> ${c.email ?? '-'}</p>
          <p><i class="fa-solid fa-phone me-2 text-primary"></i> ${c.phone ?? '-'}</p>
          <p><i class="fa-solid fa-location-dot me-2 text-primary"></i> ${c.address ?? '-'}</p>
        </div>

        <div class="societe-footer d-flex justify-content-between align-items-center mt-3">
          <div class="admin-info">
            <small>Administrateur : <strong>${adminName}</strong></small>
          </div>
          <div class="actions">
            <button class="btn-action edit" title="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn-action delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>`;
  }

  function renderPagination(totalPages) {
    if (!paginationEl) return;
    const wrapper = paginationEl.closest('nav');
    if (totalPages <= 1) {
      paginationEl.innerHTML = '';
      if (wrapper) wrapper.classList.add('d-none');
      return;
    }
    if (wrapper) wrapper.classList.remove('d-none');

    let html = `
      <li class="page-item ${currentPage===1?'disabled':''}">
        <a class="page-link" href="#" data-page="${currentPage-1}">«</a>
      </li>`;
    for (let i=1;i<=totalPages;i++){
      html += `<li class="page-item ${i===currentPage?'active':''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    html += `
      <li class="page-item ${currentPage===totalPages?'disabled':''}">
        <a class="page-link" href="#" data-page="${currentPage+1}">»</a>
      </li>`;

    paginationEl.innerHTML = html;

    paginationEl.querySelectorAll('.page-link').forEach(a=>{
      a.addEventListener('click', e=>{
        e.preventDefault();
        const p = parseInt(a.dataset.page,10);
        if (!isNaN(p) && p>=1) { currentPage = p; render(); }
      });
    });
  }

  // Row actions
  function bindRowActions() {
    // edit
    listEl.querySelectorAll('.societe-card .btn-action.edit').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        const id = btn.closest('.societe-card').dataset.id;
        try{
          const res = await fetch(`/admin/companies/${id}`);
          if (!res.ok) throw new Error('HTTP '+res.status);
          const c = await res.json();

          // Remplit le formulaire
          document.getElementById('editCompanyId').value = c.id;
          document.getElementById('editName').value = c.name ?? '';
          document.getElementById('editDomain').value = c.domain ?? '';
          document.getElementById('editEmail').value = c.email ?? '';
          document.getElementById('editPhone').value = c.phone ?? '';
          document.getElementById('editAddress').value = c.address ?? '';
          document.getElementById('editCompanyAdminSelect').value = c.admin_user_id ?? '';

          // Preview logo actuel
          const currentLogo = c.logo_path
            ? (c.logo_path.startsWith('http') ? c.logo_path : `/${c.logo_path}`)
            : '/images/placeholder_logo.jpg';
          if (editLogoPrev) editLogoPrev.src = currentLogo;
          if (editLogoCurrent) editLogoCurrent.textContent = c.logo_path ?? '—';
          if (editLogoDrop && editLogoFile && editLogoPrev) {
            bindDropArea(editLogoDrop, editLogoFile, editLogoPrev);
          }

          modalEdit?.show();
        } catch(e){ showToast('Erreur chargement société', 'danger'); }
      });
    });

    // delete
    listEl.querySelectorAll('.societe-card .btn-action.delete').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        toDeleteId = btn.closest('.societe-card').dataset.id;
        modalDelete?.show();
      });
    });
  }

  // Filters
  [adminFilter, searchInput].forEach(el =>
    el?.addEventListener('input', ()=>{ currentPage=1; render(); })
  );

  // Create
  document.getElementById('btnNewCompany')?.addEventListener('click', ()=>{
    document.getElementById('formCreateCompany')?.reset();
    if (createLogoPrev) createLogoPrev.src = '/images/placeholder_logo.jpg';
    modalCreate?.show();
  });

  document.getElementById('formCreateCompany')?.addEventListener('submit', async e=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    try{
      const res = await fetch('/admin/companies', {
        method: 'POST',
        headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
        body: fd
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      modalCreate?.hide();
      showToast('Société créée');
      await loadCompanies();
    } catch(e){ showToast('Erreur création', 'danger'); }
  });

  // Edit
  document.getElementById('formEditCompany')?.addEventListener('submit', async e=>{
    e.preventDefault();
    const id = document.getElementById('editCompanyId').value;
    const fd = new FormData(e.target);
    try{
      const res = await fetch(`/admin/companies/${id}`, {
        method: 'POST',
        headers: {
          'X-Requested-With':'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
          'X-HTTP-Method-Override': 'PUT'
        },
        body: fd
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      modalEdit?.hide();
      showToast('Société mise à jour');
      await loadCompanies();
    } catch(e){ showToast('Erreur mise à jour', 'danger'); }
  });

  // Delete
  document.getElementById('btnConfirmDeleteCompany')?.addEventListener('click', async ()=>{
    if (!toDeleteId) return;
    try{
      const res = await fetch(`/admin/companies/${toDeleteId}`, {
        method: 'DELETE',
        headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': csrf }
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      modalDelete?.hide();
      showToast('Société supprimée');
      await loadCompanies();
    } catch(e){ showToast('Erreur suppression', 'danger'); }
  });

  // Init
  fetchOptions();
  loadCompanies();
}
