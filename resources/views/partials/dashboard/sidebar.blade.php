<aside class="sidebar d-flex flex-column justify-content-between">
  <div class="p-4">
    @php($roleName = Auth::user()->role->name ?? 'employe')
    <div id="sidebar-sections"></div>
  </div>

  <div class="deconnexion p-3 border-top">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn w-100 fw-semibold">
        <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
      </button>
    </form>
  </div>
</aside>

@push('modals')
    {{-- SUPER ADMIN MODALS --}}
    @include('components.sidebarContent.superadmin.userModals')
    @include('components.sidebarContent.superadmin.companyModals')
    @include('components.sidebarContent.superadmin.teamsModals')

    {{-- ADMIN MODALS--}}
    @include('components.sidebarContent.admin.backlogModals') {{-- Backlogs --}}
    @include('components.sidebarContent.admin.congesModals') {{-- Congés / absences --}}
    @include('components.sidebarContent.admin.notesFraisModals') {{-- Notes de frais --}}
@endpush

@push('js')
<script>
(function() {
  const contentDiv = document.getElementById('dashboardContent');
  const storageCompanyKey = 'selectedCompanyId';
  const storageTeamKey = 'selectedTeamId';
  const storagePageKey = 'dashboard:lastPage:{{ Auth::id() ?? "guest" }}';
  const role = @json($roleName);
  let isLoading = false;

  function renderSidebar() {
    const container = document.getElementById('sidebar-sections');
    if (!container) return;

    const companyId = localStorage.getItem(storageCompanyKey);
    const teamId = localStorage.getItem(storageTeamKey);
    const companyName = localStorage.getItem('selectedCompanyName');
    const teamName = localStorage.getItem('selectedTeamName');
    const savedPage = localStorage.getItem(storagePageKey) || 'profil';
    let html = '';

    const icon = {
      users: 'fa-users',
      building: 'fa-building',
      gear: 'fa-gear',
      backlog: 'fa-list-check',
      calendar: 'fa-calendar-days',
      doc: 'fa-file-lines',
      plane: 'fa-plane-departure',
      receipt: 'fa-receipt',
      money: 'fa-money-check-dollar',
      org: 'fa-diagram-project',
      chat: 'fa-comments',
      blog: 'fa-newspaper',
      user: 'fa-user',
      ticket: 'fa-ticket',
      folder: 'fa-folder-open',
      news: 'fa-bullhorn'
    };

    // === SUPER ADMIN ===
    if (role === 'superadmin') {
      html += `
      <details>
        <summary class="text-uppercase small fw-bold mb-2 section-title cursor-pointer">Super-Admin</summary>
        <ul class="list-unstyled mb-0">
          <li><a href="#" data-page="usersManagement" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.users} me-2"></i>Utilisateurs & rôles</a></li>
          <li><a href="#" data-page="societes" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.building} me-2"></i>Sociétés</a></li>
          <li><a href="#" data-page="equipes" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.org} me-2"></i>Équipes</a></li>
          <!--<li><a href="#" data-page="parametresSysteme" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.gear} me-2"></i>Paramètres système</a></li>-->
        </ul>
      </details>`;
    }

    // === ENTREPRISE ===
    if (['admin', 'superadmin'].includes(role) && companyId) {
      html += `
      <details>
        <summary class="text-uppercase small fw-bold mb-2 section-title cursor-pointer">${companyName ?? 'Entreprise'}</summary>
        <ul class="list-unstyled mb-0">
          <li><a href="#" data-page="backlogs" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.backlog} me-2"></i>Gestion des Tickets</a></li>
          <li><a href="#" data-page="calendrierRH" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.calendar} me-2"></i>Calendrier RH</a></li>
          <li><a href="#" data-page="actualites" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.blog} me-2"></i>Blog & actualités</a></li>
        </ul>
      </details>`;
    }

    // === ÉQUIPE ===
    // if (teamId || companyId) {
    //   const sectionTitle = teamId ? (teamName ?? 'Équipe') : 'Mes équipes';
    //   html += `
    //   <details>
    //     <summary class="text-uppercase small fw-bold mb-2 section-title cursor-pointer">${sectionTitle}</summary>
    //     <ul class="list-unstyled mb-0">
    //       <li><a href="#" data-page="backlogs" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.backlog} me-2"></i>Backlogs</a></li>
    //       <li><a href="#" data-page="calendrierRH" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.calendar} me-2"></i>Calendrier RH</a></li>
    //       <li><a href="#" data-page="documentsRH" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.doc} me-2"></i>Documents RH</a></li>
    //       <li><a href="#" data-page="conges" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.plane} me-2"></i>Congés / absences</a></li>
    //       <li><a href="#" data-page="notesFrais" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.receipt} me-2"></i>Notes de frais</a></li>
    //       <li><a href="#" data-page="fichesPaie" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.money} me-2"></i>Fiches de paie</a></li>
    //       <li><a href="#" data-page="entretiens" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.chat} me-2"></i>Entretiens</a></li>
    //       <li><a href="#" data-page="actualites" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.blog} me-2"></i>Actualités</a></li>
    //     </ul>
    //   </details>`;
    // }

    // === ESPACE EMPLOYÉ ===
    if (['employe', 'chef_equipe', 'admin', 'superadmin'].includes(role)) {
      html += `
      <details>
        <summary class="text-uppercase small fw-bold mb-2 section-title cursor-pointer">Espace Employé</summary>
        <ul class="list-unstyled mb-0">
          <li><a href="#" data-page="profil" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.user} me-2"></i>Mon profil</a></li>
          <li><a href="#" data-page="ticketing" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.ticket} me-2"></i>Mes tickets</a></li>
          <li><a href="#" data-page="dossierRH" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.folder} me-2"></i>Mes documents</a></li>
          <li><a href="#" data-page="conges" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.plane} me-2"></i>Mes congés</a></li>
          <li><a href="#" data-page="calendrier" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.calendar} me-2"></i>Calendrier</a></li>
          <li><a href="#" data-page="actualites" class="nav-link d-block py-2 px-3 rounded"><i class="fa-solid ${icon.news} me-2"></i>Actualités</a></li>
        </ul>
      </details>`;
    }

    container.innerHTML = html;
    applyAccordionBehavior();
    bindSidebarLinks(savedPage);
  }

  // === Accordion unique ===
  function applyAccordionBehavior() {
    const details = document.querySelectorAll('#sidebar-sections details');
    details.forEach(d => {
      d.addEventListener('toggle', () => {
        if (d.open) details.forEach(o => { if (o !== d) o.removeAttribute('open'); });
      });
    });
  }

  async function loadContent(page) {
    if (isLoading) return;
    isLoading = true;
    contentDiv.innerHTML = `<p class="p-3">Chargement...</p>`;
    try {
      const response = await fetch(`/dashboard/${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
      const html = await response.text();
      contentDiv.innerHTML = html.trim().length ? html : `<p class="text-warning p-3">Aucun contenu pour "${page}".</p>`;
      localStorage.setItem(storagePageKey, page);
      const scriptKey = contentDiv.querySelector('[data-script]')?.dataset.script;
      if (scriptKey && window.pageScripts?.[scriptKey]) window.pageScripts[scriptKey]();
    } catch (e) {
      contentDiv.innerHTML = `<p class="text-danger p-3">Erreur : ${e.message}</p>`;
    } finally {
      isLoading = false;
    }
  }

  function bindSidebarLinks(savedPage) {
    const links = document.querySelectorAll('.nav-link');
    const setActive = (page) => links.forEach(l => l.classList.toggle('active', l.dataset.page === page));
    links.forEach(l => l.addEventListener('click', e => {
      e.preventDefault();
      const page = l.dataset.page;
      setActive(page);
      openParentSection(l);
      loadContent(page);
    }));

    setActive(savedPage);
    openSectionForPage(savedPage);
    loadContent(savedPage);
  }

  function openParentSection(link) {
    const parent = link.closest('details');
    if (parent) {
      parent.setAttribute('open', '');
      document.querySelectorAll('#sidebar-sections details').forEach(d => {
        if (d !== parent) d.removeAttribute('open');
      });
    }
  }

  function openSectionForPage(page) {
    const link = document.querySelector(`.nav-link[data-page="${page}"]`);
    if (link) openParentSection(link);
  }

  renderSidebar();
  window.addEventListener('storage', (e) => {
    if ([storageCompanyKey, storageTeamKey].includes(e.key)) renderSidebar();
  });
})();
</script>
@endpush
