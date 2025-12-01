/**
 * Gestion des équipes (Super Admin)
 * - Filtre société
 * - Pagination client (15/page)
 * - Modals CRUD
 * - Toasts Bootstrap
 */
export default function initTeamsManagement() {
  const bs = window.bootstrap;
  if (!bs?.Modal) return;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const listEl = document.getElementById('teamsList');
  const filterCompany = document.getElementById('filter-societe');
  const paginationEl = document.getElementById('teamsPagination');

  const modalCreate = new bs.Modal('#modalTeamCreate');
  const modalEdit   = new bs.Modal('#modalTeamEdit');
  const modalDelete = new bs.Modal('#modalTeamDelete');

  function showToast(msg, type='success') {
    const wrap = document.createElement('div');
    wrap.innerHTML = `<div class="toast align-items-center text-bg-${type} border-0"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
    const toastEl = wrap.firstElementChild;
    const container = document.getElementById('toastContainer') || (() => {
      const c = document.createElement('div');
      c.id = 'toastContainer';
      c.className = 'toast-container position-fixed top-0 end-0 p-3';
      document.body.appendChild(c);
      return c;
    })();
    container.appendChild(toastEl);
    new bs.Toast(toastEl, { delay: 3000 }).show();
  }

  let cacheTeams = [];
  let cacheCompanies = [];
  let cacheLeaders = [];
  let currentPage = 1;
  const perPage = 15;
  let toDeleteId = null;

  async function fetchOptions() {
    try {
      const res = await fetch('/admin/teams/options');
      const data = await res.json();
      cacheCompanies = data.companies;
      cacheLeaders = data.leaders;

      // remplis filtre société
      filterCompany.innerHTML = `<option value="">Toutes</option>` +
        cacheCompanies.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

      // remplis selects modals
      const companySelects = document.querySelectorAll('#createTeamCompanySelect,#editTeamCompanySelect');
      const leaderSelects  = document.querySelectorAll('#createTeamLeaderSelect,#editTeamLeaderSelect');

      const htmlCompanies = cacheCompanies.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
      const htmlLeaders   = `<option value="">Aucun</option>` +
        cacheLeaders.map(l => `<option value="${l.id}">${l.last_name.toUpperCase()} ${l.first_name}</option>`).join('');

      companySelects.forEach(s => s.innerHTML = htmlCompanies);
      leaderSelects.forEach(s => s.innerHTML = htmlLeaders);
    } catch (e) { showToast('Erreur chargement options','danger'); }
  }

  async function loadTeams() {
    listEl.innerHTML = '';
    try {
      const res = await fetch('/admin/teams');
      cacheTeams = await res.json();
      render();
    } catch (e) { listEl.innerHTML = '<p class="text-danger">Erreur chargement</p>'; }
  }

  function render() {
    const companyId = filterCompany.value;
    const filtered = cacheTeams.filter(t => !companyId || t.company_id === companyId);

    const totalPages = Math.ceil(filtered.length / perPage);
    if (currentPage > Math.max(totalPages, 1)) currentPage = 1;

    const start = (currentPage - 1) * perPage;
    const pageData = filtered.slice(start, start + perPage);

    listEl.innerHTML = pageData.length ? pageData.map(cardHTML).join('') :
      '<div class="text-muted">Aucune équipe</div>';

    renderPagination(totalPages);
    bindActions();
  }

  // Hue déterministe à partir du nom de société (0..360)
    function hashHue(str){
    let h=0; for(let i=0;i<str.length;i++){ h = str.charCodeAt(i) + ((h<<5) - h); h|=0; }
    return Math.abs(h)%360;
    }
    // Construit l’attribut style pour le badge société
    function companyBadgeStyle(name){
    if(!name) return '';
    const h = hashHue(String(name));
    return `--badge-h:${h};--badge-s:75%;--badge-l:60%`;
    }


    function cardHTML(t) {
    const companyName = t.company?.name ?? '-';
    const leaderName = t.leader ? `${t.leader.first_name} ${t.leader.last_name}` : '-';
    const styleHsl   = companyName && companyName !== '-' ? ` style="${companyBadgeStyle(companyName)}"` : '';

    return `
        <div class="equipe-card" data-id="${t.id}">
        <div class="equipe-header d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0">${t.name}</h4>
            <span class="badge societe"${styleHsl}>${companyName}</span>
        </div>

        <p class="mb-2">
            <i class="fa-solid fa-user-tie me-2 icon-leader"></i>
            Chef d’équipe : <strong>${leaderName}</strong>
        </p>
        <p class="mb-2">
            <i class="fa-solid fa-users me-2 icon-members"></i>
            Membres : ${t.users_count ?? 0} employés
        </p>

        <div class="actions mt-3">
            <button class="btn-action edit" title="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn-action delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    let html = `<li class="page-item ${currentPage===1?'disabled':''}">
      <a class="page-link" href="#" data-page="${currentPage-1}">«</a></li>`;
    for (let i=1;i<=totalPages;i++){
      html += `<li class="page-item ${i===currentPage?'active':''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    html += `<li class="page-item ${currentPage===totalPages?'disabled':''}">
      <a class="page-link" href="#" data-page="${currentPage+1}">»</a></li>`;
    paginationEl.innerHTML = html;
    paginationEl.querySelectorAll('.page-link').forEach(a=>{
      a.addEventListener('click',e=>{
        e.preventDefault();
        const p=parseInt(a.dataset.page,10);
        if(!isNaN(p)&&p>=1){currentPage=p;render();}
      });
    });
  }

  function bindActions() {
    listEl.querySelectorAll('.btn-action.edit').forEach(btn=>{
      btn.addEventListener('click',async()=>{
        const id=btn.closest('.equipe-card').dataset.id;
        const res=await fetch(`/admin/teams/${id}`); const t=await res.json();
        document.getElementById('editTeamId').value=t.id;
        document.getElementById('editTeamName').value=t.name??'';
        document.getElementById('editTeamDesc').value=t.description??'';
        document.getElementById('editTeamCompanySelect').value=t.company_id??'';
        document.getElementById('editTeamLeaderSelect').value=t.leader_user_id??'';
        modalEdit.show();
      });
    });
    listEl.querySelectorAll('.btn-action.delete').forEach(btn=>{
      btn.addEventListener('click',()=>{
        toDeleteId=btn.closest('.equipe-card').dataset.id;
        modalDelete.show();
      });
    });
  }

  // Create
  document.getElementById('btnNewTeam')?.addEventListener('click',()=>{
    document.getElementById('formCreateTeam').reset();
    modalCreate.show();
  });

  document.getElementById('formCreateTeam')?.addEventListener('submit',async e=>{
    e.preventDefault();
    const fd=new FormData(e.target);
    const res=await fetch('/admin/teams',{method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:fd});
    if(res.ok){modalCreate.hide();showToast('Équipe créée');loadTeams();}else showToast('Erreur création','danger');
  });

  document.getElementById('formEditTeam')?.addEventListener('submit',async e=>{
    e.preventDefault();
    const id=document.getElementById('editTeamId').value;
    const fd=new FormData(e.target);
    const res=await fetch(`/admin/teams/${id}`,{
      method:'POST',headers:{'X-CSRF-TOKEN':csrf,'X-HTTP-Method-Override':'PUT'},body:fd
    });
    if(res.ok){modalEdit.hide();showToast('Équipe mise à jour');loadTeams();}else showToast('Erreur MAJ','danger');
  });

  document.getElementById('btnConfirmDeleteTeam')?.addEventListener('click',async()=>{
    if(!toDeleteId)return;
    const res=await fetch(`/admin/teams/${toDeleteId}`,{
      method:'DELETE',headers:{'X-CSRF-TOKEN':csrf}
    });
    if(res.ok){modalDelete.hide();showToast('Équipe supprimée');loadTeams();}else showToast('Erreur suppression','danger');
  });

  filterCompany?.addEventListener('change',()=>{currentPage=1;render();});

  fetchOptions();
  loadTeams();
}
