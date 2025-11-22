// resources/js/components/expensesManagement.js
export default function initExpensesManagement() {
    console.log('%c[expensesManagement] Initialisation', 'color: orange');

    const page = document.querySelector('.notes-admin-page');
    if (!page) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const tableBody = document.getElementById('expensesTableBody');
    const statsEls = page.querySelectorAll('.notes-stats .stat-card p');

    const selectEmployee = document.getElementById('filter-expense-employee');
    const selectStatus = document.getElementById('filter-expense-status');
    const selectType = document.getElementById('filter-expense-type');
    const inputStart = document.getElementById('filter-expense-start');
    const inputEnd = document.getElementById('filter-expense-end');
    const btnReset = document.getElementById('btnExpensesReset');

    const companyId = localStorage.getItem('selectedCompanyId');
    const teamId = localStorage.getItem('selectedTeamId');

    // Modal détails
    const modalEl = document.getElementById('modalExpenseDetails');
    const modalDetails = modalEl ? new window.bootstrap.Modal(modalEl) : null;

    const elDetailType = document.getElementById('expenseDetailType');
    const elDetailStatus = document.getElementById('expenseDetailStatus');
    const elDetailAmount = document.getElementById('expenseDetailAmount');
    const elDetailDate = document.getElementById('expenseDetailDate');
    const elDetailUser = document.getElementById('expenseDetailUser');
    const elDetailCompany = document.getElementById('expenseDetailCompany');
    const elDetailDescription = document.getElementById('expenseDetailDescription');
    const elDetailReceipt = document.getElementById('expenseDetailReceipt');

    let expensesCache = [];

    /* ---------------------- Helpers ----------------------- */

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString('fr-FR');
    }

    function formatAmount(amount) {
        if (amount == null) return '—';
        const n = Number(amount);
        if (Number.isNaN(n)) return amount;
        return n.toFixed(2).replace('.', ',') + ' €';
    }

    function typeLabel(type) {
        switch (type) {
            case 'peage':
                return 'Péage / autoroute';
            case 'repas':
                return 'Repas';
            case 'hebergement':
                return 'Hébergement';
            case 'km':
                return 'Kilométrage';
            default:
                return type || '—';
        }
    }

    function statusLabel(status) {
        switch (status) {
            case 'pending':
                return 'En attente';
            case 'approved':
                return 'Validé';
            case 'rejected':
                return 'Refusé';
            case 'paid':
                return 'Payé';
            default:
                return status || '—';
        }
    }

    function statusClass(status) {
        switch (status) {
            case 'pending':
                return 'en-attente';
            case 'approved':
                return 'valide';
            case 'rejected':
                return 'refuse';
            case 'paid':
                return 'valide'; // ou "paid" si tu ajoutes un style spécifique
            default:
                return '';
        }
    }

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
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 mb-2" role="alert">
          <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>`;
        const toastEl = wrap.firstElementChild;
        container.appendChild(toastEl);
        new bs.Toast(toastEl, { delay: 3000 }).show();
    }

    /* ---------------------- Chargement -------------------- */

    async function loadExpenses() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-3">
                    Chargement des notes de frais...
                </td>
            </tr>
        `;

        try {
            const url = new URL('/admin/expenses', window.location.origin);
            if (companyId) url.searchParams.set('company_id', companyId);
            if (teamId) url.searchParams.set('team_id', teamId);

            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            // Selon ton controller : soit { expenses: [...], stats: {...} }, soit juste [...]
            expensesCache = Array.isArray(data) ? data : (data.expenses || []);

            // Stats globales si renvoyées par le back
            if (!Array.isArray(data) && data.stats) {
                updateStats(data.stats);
            } else {
                recomputeStatsFromCache();
            }

            fillEmployeeFilter(expensesCache);
            applyFiltersAndRender();
        } catch (err) {
            console.error(err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger py-3">
                        Erreur de chargement : ${err.message}
                    </td>
                </tr>
            `;
        }
    }

    function fillEmployeeFilter(expenses) {
        if (!selectEmployee) return;
        const map = new Map();
        expenses.forEach((e) => {
            if (e.user) {
                map.set(
                    e.user.id,
                    `${e.user.first_name ?? ''} ${e.user.last_name ?? ''}`.trim()
                );
            }
        });

        selectEmployee.innerHTML =
            `<option value="">Tous</option>` +
            Array.from(map.entries())
                .map(([id, name]) => `<option value="${id}">${name}</option>`)
                .join('');
    }

    /* ---------------------- Filtres + rendu -------------- */

    function applyFiltersAndRender() {
        const employeeId = selectEmployee?.value || '';
        const status = selectStatus?.value || '';
        const type = selectType?.value || '';
        const startVal = inputStart?.value || '';
        const endVal = inputEnd?.value || '';

        let filtered = expensesCache.slice();

        if (employeeId) {
            filtered = filtered.filter((e) => e.user_id === employeeId);
        }

        if (status) {
            filtered = filtered.filter((e) => e.status === status);
        }

        if (type) {
            filtered = filtered.filter((e) => e.type === type);
        }

        if (startVal) {
            const startDate = new Date(startVal);
            filtered = filtered.filter((e) => {
                const d = new Date(e.created_at);
                return d >= startDate;
            });
        }

        if (endVal) {
            const endDate = new Date(endVal);
            // On considère jusqu'à 23:59:59
            endDate.setHours(23, 59, 59, 999);
            filtered = filtered.filter((e) => {
                const d = new Date(e.created_at);
                return d <= endDate;
            });
        }

        renderExpenses(filtered);
        // Les stats restent globales (comme congés), donc pas recalculées ici
    }

    function renderExpenses(expenses) {
        if (!expenses.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        Aucune note de frais trouvée.
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = expenses
            .map((e) => {
                const employeeName = e.user
                    ? `${e.user.first_name ?? ''} ${e.user.last_name ?? ''}`.trim()
                    : '—';

                const type = typeLabel(e.type);
                const amount = formatAmount(e.amount);
                const date = formatDate(e.created_at);
                const sLabel = statusLabel(e.status);
                const sClass = statusClass(e.status);

                const receiptHtml = e.receipt_url
                    ? `<a href="${e.receipt_url}" target="_blank" rel="noopener">Voir</a>`
                    : e.receipt_path
                    ? `<span>${e.receipt_path}</span>`
                    : '<span class="text-muted">—</span>';

                let actions = `
                    <button class="btn-action details" data-id="${e.id}" title="Détails">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                `;

                if (e.status === 'pending') {
                    actions = `
                        <button class="btn-action valide" data-id="${e.id}" title="Valider">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button class="btn-action refuse" data-id="${e.id}" title="Refuser">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        ${actions}
                    `;
                } else if (e.status === 'approved') {
                    actions = `
                        <button class="btn-action pay" data-id="${e.id}" title="Marquer comme payé">
                            <i class="fa-solid fa-money-bill-1-wave"></i>
                        </button>
                        ${actions}
                    `;
                }

                return `
                    <tr>
                        <td><strong>${employeeName}</strong></td>
                        <td>${type}</td>
                        <td>${e.description ?? '—'}</td>
                        <td>${amount}</td>
                        <td>${receiptHtml}</td>
                        <td>${date}</td>
                        <td><span class="status ${sClass}">${sLabel}</span></td>
                        <td>
                            <div class="table-actions">
                                ${actions}
                            </div>
                        </td>
                    </tr>
                `;
            })
            .join('');
    }

    function updateStats(stats) {
        const [totalEl, pendingEl, approvedEl, rejectedEl] = statsEls;
        if (!totalEl) return;

        totalEl.textContent = stats.total ?? '0';
        pendingEl.textContent = stats.pending ?? '0';
        approvedEl.textContent = stats.approved ?? '0';
        rejectedEl.textContent = stats.rejected ?? '0';
    }

    function recomputeStatsFromCache() {
        const stats = {
            total: expensesCache.length,
            pending: expensesCache.filter((e) => e.status === 'pending').length,
            approved: expensesCache.filter((e) => e.status === 'approved').length,
            rejected: expensesCache.filter((e) => e.status === 'rejected').length,
        };
        updateStats(stats);
    }

    /* ---------------------- Détails + actions ------------- */

    async function openDetails(id) {
        if (!modalDetails) return;

        try {
            const res = await fetch(`/admin/expenses/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const e = await res.json();

            // Type badge
            elDetailType.textContent = typeLabel(e.type);
            elDetailType.className = 'badge expense-type-badge ' + (e.type || '');

            // Statut badge
            const sLabel = statusLabel(e.status);
            elDetailStatus.textContent = sLabel;
            elDetailStatus.className =
                'badge expense-status-badge ' + (e.status || '');

            // Montant / date
            elDetailAmount.textContent = formatAmount(e.amount);
            elDetailDate.textContent = formatDate(e.created_at);

            // Employé / société
            const userName = e.user
                ? `${e.user.first_name ?? ''} ${e.user.last_name ?? ''}`.trim()
                : '—';
            elDetailUser.textContent = userName;

            elDetailCompany.textContent = e.company?.name ?? '—';

            // Description
            elDetailDescription.textContent = e.description ?? '—';

            // Justificatif
            if (e.receipt_url) {
                elDetailReceipt.innerHTML = `<a href="${e.receipt_url}" target="_blank" rel="noopener">Ouvrir le justificatif</a>`;
            } else if (e.receipt_path) {
                elDetailReceipt.textContent = e.receipt_path;
            } else {
                elDetailReceipt.textContent = '—';
            }

            modalDetails.show();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors du chargement de la note de frais", "error");
        }
    }

    async function updateStatus(id, newStatus) {
        try {
            const res = await fetch(`/admin/expenses/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ status: newStatus }),
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            let msg = 'Statut mis à jour';
            if (newStatus === 'approved') msg = 'Note de frais validée';
            if (newStatus === 'rejected') msg = 'Note de frais refusée';
            if (newStatus === 'paid') msg = 'Note de frais marquée comme payée';

            showToast(msg, 'success');

            // On recharge la liste depuis le serveur pour être sûr
            await loadExpenses();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la mise à jour du statut", "error");
        }
    }

    tableBody.addEventListener('click', (e) => {
        const btnDetails = e.target.closest('.btn-action.details');
        const btnValide = e.target.closest('.btn-action.valide');
        const btnRefuse = e.target.closest('.btn-action.refuse');
        const btnPay = e.target.closest('.btn-action.pay');

        const btn = btnDetails || btnValide || btnRefuse || btnPay;
        if (!btn) return;

        const id = btn.dataset.id;
        if (!id) return;

        if (btnDetails) {
            openDetails(id);
        } else if (btnValide) {
            updateStatus(id, 'approved');
        } else if (btnRefuse) {
            updateStatus(id, 'rejected');
        } else if (btnPay) {
            updateStatus(id, 'paid');
        }
    });

    /* ---------------------- Listeners filtres ------------- */

    [selectEmployee, selectStatus, selectType, inputStart, inputEnd].forEach(
        (el) => {
            el?.addEventListener('change', () => {
                applyFiltersAndRender();
            });
        }
    );

    btnReset?.addEventListener('click', () => {
        if (selectEmployee) selectEmployee.value = '';
        if (selectStatus) selectStatus.value = '';
        if (selectType) selectType.value = '';
        if (inputStart) inputStart.value = '';
        if (inputEnd) inputEnd.value = '';
        applyFiltersAndRender();
    });

    /* ---------------------- Init -------------------------- */

    loadExpenses();
}
