export default function initDocumentsManagement() {
    console.log('%c[documentsManagement] Initialisation', 'color: lightblue');

    const page = document.querySelector('.documents-admin-page');
    if (!page) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // --- Sélecteurs ---
    const tableBody = document.getElementById('documentsTableBody');
    const statsCards = page.querySelectorAll('.document-stats .stat-card p');

    const filterEmployee = document.getElementById('filter-document-employee');
    const filterType = document.getElementById('filter-document-type');
    const filterStatus = document.getElementById('filter-document-status');
    const searchInput = document.getElementById('search-document');

    const companyId = localStorage.getItem('selectedCompanyId');
    const teamId = localStorage.getItem('selectedTeamId');

    let documentsCache = [];

    // === TOAST helper (même pattern que les autres modules) ===
    function showToast(message, type = 'success') {
        const bs = window.bootstrap;
        if (!bs?.Toast) {
            alert(message);
            return;
        }

        const container =
            document.getElementById('toastContainer') ||
            (() => {
                const c = document.createElement('div');
                c.id = 'toastContainer';
                c.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(c);
                return c;
            })();

        const wrap = document.createElement('div');
        wrap.innerHTML = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>`;
        const toastEl = wrap.firstElementChild;
        container.appendChild(toastEl);
        new bs.Toast(toastEl, { delay: 3000 }).show();
    }

    // === Helpers ===
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString('fr-FR');
    }

    function typeLabel(type) {
        switch (type) {
            case 'CNI':
                return "Carte d'identité";
            case 'Carte Vitale':
                return 'Carte Vitale';
            case 'Permis':
                return 'Permis de conduire';
            case 'Contrat':
                return 'Contrat';
            case 'Fiche Fonction':
                return 'Fiche Fonction';
            default:
                return type || '-';
        }
    }

    function statusLabel(status) {
        switch (status) {
            case 'valid':
            case 'validated':
                return 'Validé';
            case 'pending':
                return 'En attente';
            case 'rejected':
                return 'Refusé';
            case 'expired':
                return 'Expiré';
            default:
                return status || '-';
        }
    }

    // classes CSS pour coller à ton design (.status valide / en-attente / refuse / expire)
    function statusClass(status) {
        switch (status) {
            case 'valid':
            case 'validated':
                return 'valide';
            case 'pending':
                return 'en-attente';
            case 'rejected':
                return 'refuse';
            case 'expired':
                return 'expire';
            default:
                return '';
        }
    }

    // === Chargement documents ===
    async function loadDocuments() {
        if (!tableBody) return;

        tableBody.innerHTML = `
            <tr>
              <td colspan="5" class="text-center  py-3">
                Chargement des documents...
              </td>
            </tr>`;

        try {
            const url = new URL('/admin/documents', window.location.origin);

            if (companyId) url.searchParams.set('company_id', companyId);
            if (teamId) url.searchParams.set('team_id', teamId);

            const employeeId = filterEmployee?.value || '';
            const type = filterType?.value || '';
            const status = filterStatus?.value || '';
            const search = searchInput?.value.trim() || '';

            if (employeeId) url.searchParams.set('employee_id', employeeId);
            if (type) url.searchParams.set('type', type);
            if (status) url.searchParams.set('status', status);
            if (search) url.searchParams.set('search', search);

            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            documentsCache = data.documents || [];

            renderDocuments(documentsCache);
            updateStats(data.stats || {});
            fillEmployeesFilter(documentsCache);
        } catch (err) {
            console.error(err);
            tableBody.innerHTML = `
              <tr>
                <td colspan="5" class="text-center text-danger py-3">
                  Erreur de chargement : ${err.message}
                </td>
              </tr>`;
        }
    }

    function renderDocuments(documents) {
        if (!documents.length) {
            tableBody.innerHTML = `
              <tr>
                <td colspan="5" class="text-center  py-3">
                  Aucun document trouvé.
                </td>
              </tr>`;
            return;
        }

        tableBody.innerHTML = documents
            .map((d) => {
                const employeeName = d.user
                    ? `${d.user.first_name} ${d.user.last_name}`
                    : '—';

                const type = typeLabel(d.type);
                const uploaded = formatDate(d.uploaded_at);
                const sLabel = statusLabel(d.status);
                const sClass = statusClass(d.status);

                // Construire URL du fichier si dispo
                const fileUrl = d.file_path
                    ? d.file_path.startsWith('http')
                        ? d.file_path
                        : `/${d.file_path.replace(/^\/+/, '')}`
                    : null;

                const actionsHtml = `
                    <div class="table-actions">
                        ${
                            fileUrl
                                ? `<button class="btn-action download" data-url="${fileUrl}" title="Télécharger / ouvrir">
                                     <i class="fa-solid fa-download"></i>
                                   </button>`
                                : ''
                        }
                        ${
                            d.status === 'pending'
                                ? `
                            <button class="btn-action valide" data-id="${d.id}" title="Valider">
                              <i class="fa-solid fa-check"></i>
                            </button>
                            <button class="btn-action refuse" data-id="${d.id}" title="Refuser">
                              <i class="fa-solid fa-xmark"></i>
                            </button>`
                                : ''
                        }
                        <button class="btn-action delete" data-id="${d.id}" title="Supprimer">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;

                return `
                  <tr>
                    <td><strong>${employeeName}</strong></td>
                    <td>${type}</td>
                    <td>${uploaded}</td>
                    <td>
                      <span class="status ${sClass}">${sLabel}</span>
                    </td>
                    <td>${actionsHtml}</td>
                  </tr>`;
            })
            .join('');
    }

    function updateStats(stats) {
        const [totalEl, validatedEl, pendingEl, refusedExpiredEl] = statsCards;
        if (!totalEl) return;

        totalEl.textContent = stats.total ?? '0';
        validatedEl.textContent = stats.validated ?? '0';
        pendingEl.textContent = stats.pending ?? '0';
        refusedExpiredEl.textContent = stats.refused_expired ?? '0';
    }

    function fillEmployeesFilter(documents) {
        if (!filterEmployee) return;

        const current = filterEmployee.value;
        const users = {};
        documents.forEach((d) => {
            if (d.user) {
                users[d.user.id] = `${d.user.last_name.toUpperCase()} ${
                    d.user.first_name
                }`;
            }
        });

        const options =
            '<option value="">Tous</option>' +
            Object.entries(users)
                .map(
                    ([id, label]) =>
                        `<option value="${id}" ${
                            id === current ? 'selected' : ''
                        }>${label}</option>`
                )
                .join('');

        filterEmployee.innerHTML = options;
    }

    // === Listeners filtres ===
    [filterEmployee, filterType, filterStatus].forEach((el) => {
        el?.addEventListener('change', () => {
            loadDocuments();
        });
    });

    searchInput?.addEventListener('input', () => {
        // petite latence optionnelle possible, mais on reste simple
        loadDocuments();
    });

    // === Actions (délégation sur le tbody) ===
    tableBody?.addEventListener('click', async (e) => {
        const btnDownload = e.target.closest('.btn-action.download');
        const btnValide = e.target.closest('.btn-action.valide');
        const btnRefuse = e.target.closest('.btn-action.refuse');
        const btnDelete = e.target.closest('.btn-action.delete');

        // Télécharger / ouvrir
        if (btnDownload) {
            const url = btnDownload.dataset.url;
            if (url) {
                window.open(url, '_blank');
            } else {
                showToast('Aucun fichier disponible pour ce document', 'error');
            }
            return;
        }

        // Valider / Refuser
        if (btnValide || btnRefuse) {
            const id = (btnValide || btnRefuse).dataset.id;
            if (!id) return;

            const newStatus = btnValide ? 'validated' : 'rejected';

            try {
                const res = await fetch(`/admin/documents/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                showToast(
                    `Document ${
                        newStatus === 'validated' ? 'validé' : 'refusé'
                    }`,
                    'success'
                );
                loadDocuments();
            } catch (err) {
                console.error(err);
                showToast(
                    'Erreur lors de la mise à jour du document',
                    'error'
                );
            }
            return;
        }

        // Suppression
        if (btnDelete) {
            const id = btnDelete.dataset.id;
            if (!id) return;

            if (!confirm('Supprimer ce document ?')) return;

            try {
                const res = await fetch(`/admin/documents/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                showToast('Document supprimé', 'success');
                loadDocuments();
            } catch (err) {
                console.error(err);
                showToast('Erreur lors de la suppression du document', 'error');
            }
        }
    });

    // === INIT ===
    loadDocuments();
}
