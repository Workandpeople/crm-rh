export default function initBacklogsManagement() {
  console.log('Initialisation de la gestion des backlogs');

  const page = document.querySelector('.ticketing-admin-page');
  if (!page) return;

  const filters = page.querySelectorAll('.filter-btn');
  const list = page.querySelector('.ticket-list');
  const statsCards = page.querySelectorAll('.stat-card p');
  const companyId = localStorage.getItem('selectedCompanyId');
  const teamId = localStorage.getItem('selectedTeamId');

  // Chargement initial
  loadTickets('all');

  // Filtres
  filters.forEach(btn => {
    btn.addEventListener('click', () => {
      filters.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadTickets(btn.dataset.type);
    });
  });

  async function loadTickets(type = 'all') {
    list.innerHTML = `<p class="text-muted p-3">Chargement...</p>`;
    try {
      const url = new URL(`/admin/backlogs`, window.location.origin);
      url.searchParams.set('type', type);
      url.searchParams.set('mode', 'ajax');
      if (companyId) url.searchParams.set('company_id', companyId);
      if (teamId) url.searchParams.set('team_id', teamId);

      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const data = await res.json();
      renderTickets(data.tickets);
      updateStats(data.stats);
    } catch (err) {
      list.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${err.message}</p>`;
    }
  }

  function renderTickets(tickets) {
    if (!tickets.length) {
      list.innerHTML = `<p class="text-muted p-3">Aucun ticket trouvé.</p>`;
      return;
    }

    list.innerHTML = tickets.map(t => `
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="d-flex align-items-center gap-2">
            <span class="ticket-type ${t.type}">
              ${icon(t.type)} ${t.type.replace('_',' ')}
            </span>
            <span class="ticket-user">${t.creator?.full_name ?? 'Utilisateur inconnu'}</span>
          </div>
          <span class="ticket-status ${t.status}">${t.status}</span>
        </div>
        <h5 class="ticket-title">${t.title}</h5>
        <p class="ticket-desc">${t.description ?? ''}</p>
        <div class="ticket-footer">
          <small>Créé le ${formatDate(t.created_at)}</small>
          <div class="actions">
            ${t.status === 'en_attente' ? `
              <button class="btn-action valide" data-id="${t.id}"><i class="fa-solid fa-check"></i></button>
              <button class="btn-action refuse" data-id="${t.id}"><i class="fa-solid fa-xmark"></i></button>
            ` : ''}
            <button class="btn-action details" data-id="${t.id}"><i class="fa-solid fa-eye"></i></button>
          </div>
        </div>
      </div>
    `).join('');
  }

  function updateStats(stats) {
    const [total, pending, validated, refused] = statsCards;
    total.textContent = stats.total;
    pending.textContent = stats.pending;
    validated.textContent = stats.validated;
    refused.textContent = stats.refused;
  }

  function icon(type) {
    switch (type) {
      case 'conge': return '<i class="fa-solid fa-plane-departure"></i>';
      case 'note_frais': return '<i class="fa-solid fa-receipt"></i>';
      case 'incident': return '<i class="fa-solid fa-triangle-exclamation"></i>';
      default: return '<i class="fa-solid fa-circle-question"></i>';
    }
  }

  function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('fr-FR');
  }
}
