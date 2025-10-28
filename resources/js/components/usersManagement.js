/**
 * resources/js/components/usersManagement.js
 * Gère la page "Gestion des utilisateurs" du CRM RH.
 */

export default function initUsersManagement() {
    console.log('%c[usersManagement] Initialisation', 'color: cyan');

    const tableBody = document.getElementById('usersTableBody');
    const roleFilter = document.getElementById('filter-role');
    const statusFilter = document.getElementById('filter-statut');
    const companyFilter = document.getElementById('filter-societe');
    if (!tableBody) return; // sécurité si le DOM ne correspond pas

    async function loadUsers() {
        console.log('[usersManagement] Chargement des utilisateurs…');
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>`;
        try {
            const res = await fetch('/admin/users', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const users = await res.json();

            renderUsers(users);
            fillCompanyFilter(users);
            console.log(`[usersManagement] ${users.length} utilisateurs reçus`);
        } catch (error) {
            console.error('[usersManagement] Erreur:', error);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-danger text-center py-4">
                Erreur de chargement (${error.message})
            </td></tr>`;
        }
    }

    function fillCompanyFilter(users) {
        const companies = [...new Set(users.map(u => u.company?.name).filter(Boolean))];
        companyFilter.innerHTML = `<option value="">Toutes</option>` + companies.map(c => `<option>${c}</option>`).join('');
    }

    function renderUsers(users) {
        const roleVal = roleFilter.value;
        const statusVal = statusFilter.value;
        const companyVal = companyFilter.value;

        const filtered = users.filter(u => {
            const matchRole = !roleVal || u.role?.name === roleVal;
            const matchStatus = !statusVal || u.status === statusVal;
            const matchCompany = !companyVal || u.company?.name === companyVal;
            return matchRole && matchStatus && matchCompany;
        });

        if (!filtered.length) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Aucun utilisateur trouvé.</td></tr>`;
            return;
        }

        tableBody.innerHTML = filtered.map(u => `
            <tr>
                <td><strong>${u.first_name} ${u.last_name}</strong></td>
                <td>${u.email}</td>
                <td><span class="role ${u.role?.name ?? ''}">${roleLabel(u.role?.name)}</span></td>
                <td>${u.company?.name ?? '-'}</td>
                <td><span class="status ${statusClass(u.status)}">${statusLabel(u.status)}</span></td>
                <td>${u.last_login_at ? formatDate(u.last_login_at) : '-'}</td>
                <td>
                    <div class="table-actions">
                        <button class="btn-action edit" data-id="${u.id}"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-action delete" data-id="${u.id}"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // === utilitaires ===
    const roleLabel = r => {
        if (r === 'superadmin') return 'Super Admin';
        if (r === 'admin') return 'Admin';
        if (r === 'chef_equipe') return 'Chef d’équipe';
        if (r === 'employe') return 'Employé';
        return '-';
    };
    const statusLabel = s => s === 'active' ? 'Actif' : (s === 'inactive' ? 'Inactif' : 'En attente');
    const statusClass = s => s === 'active' ? 'actif' : (s === 'inactive' ? 'inactif' : 'pending');
    const formatDate = d => {
        const date = new Date(d);
        return date.toLocaleDateString('fr-FR') + ' à ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    };

    [roleFilter, statusFilter, companyFilter].forEach(el => el.addEventListener('change', () => loadUsers()));
    loadUsers();
}
