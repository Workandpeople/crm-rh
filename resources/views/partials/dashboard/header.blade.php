<header class="header">
    {{-- Gauche : Logo + Sélecteurs --}}
    <div class="header-left">
        <a href="{{ route('dashboard') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
            <span class="logo-title">CRM RH</span>
        </a>

        @php
            $user = auth()->user();
            $role = $user->role?->name ?? '';
        @endphp

        <div id="selectorsArea" class="selects">
            @if(in_array($role, ['superadmin', 'admin']))
                <select id="selectCompany" class="select">
                    <option value="">— Entreprise —</option>
                </select>
            @endif

            @if(in_array($role, ['superadmin', 'admin', 'chef_equipe']))
                <select id="selectTeam" class="select">
                    <option value="">— Équipe —</option>
                </select>
            @endif
        </div>
    </div>

    {{-- Droite : Profil --}}
    <div class="header-right">
        <button id="profileMenuToggle" class="user" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="profileDropdown">
            <span class="user-avatar">
                <img src="{{ asset('images/avatar.png') }}" alt="Avatar">
            </span>
            <span class="user-name">{{ auth()->user()->first_name ?? 'Utilisateur' }}</span>
            <i class="fa-solid fa-chevron-down user-chevron" aria-hidden="true"></i>
        </button>

        <div id="profileDropdown" class="menu" role="menu" aria-hidden="true">
            <a href="#" class="menu-item" role="menuitem">Mon profil</a>
            <a href="#" class="menu-item" role="menuitem">Paramètres</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="menu-item menu-item-danger" role="menuitem">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
                </button>
            </form>
        </div>
    </div>
</header>

{{-- JS --}}
@push('js')
<script>
(function () {
    const role = @json($role);
    const selectCompany = document.getElementById('selectCompany');
    const selectTeam = document.getElementById('selectTeam');

    const savedCompanyId = localStorage.getItem('selectedCompanyId');
    const savedTeamId = localStorage.getItem('selectedTeamId');

    // ===== DROPDOWN PROFIL =====
    const toggleBtn = document.getElementById('profileMenuToggle');
    const dropdown = document.getElementById('profileDropdown');

    function closeDropdown() {
        dropdown.classList.remove('is-open');
        dropdown.setAttribute('aria-hidden', 'true');
        toggleBtn.setAttribute('aria-expanded', 'false');
    }
    function openDropdown() {
        dropdown.classList.add('is-open');
        dropdown.setAttribute('aria-hidden', 'false');
        toggleBtn.setAttribute('aria-expanded', 'true');
    }

    toggleBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = dropdown.classList.contains('is-open');
        isOpen ? closeDropdown() : openDropdown();
    });
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
            closeDropdown();
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDropdown();
    });

    // ===== HELPERS FETCH =====
    const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };

    function setLoading(selectEl, isLoading) {
        if (!selectEl) return;
        selectEl.disabled = !!isLoading;
        if (isLoading) {
            const txt = selectEl.id === 'selectCompany' ? 'Chargement des entreprises…' : 'Chargement des équipes…';
            selectEl.innerHTML = `<option value="">${txt}</option>`;
        }
    }

    async function fetchJson(url) {
        const res = await fetch(url, { headers });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        // si un contrôleur renvoie du HTML (erreur), on évite le crash JSON:
        const text = await res.text();
        try { return JSON.parse(text); } catch { throw new Error('Réponse non-JSON'); }
    }

    // ===== CHARGEMENT ENTREPRISES =====
    async function fetchCompanies() {
        if (!selectCompany) return;
        try {
            setLoading(selectCompany, true);
            const companies = await fetchJson('/admin/companies');
            selectCompany.innerHTML = `<option value="">— Sélectionner une entreprise —</option>` +
                companies.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            if (savedCompanyId) {
                selectCompany.value = savedCompanyId;
                await fetchTeams(savedCompanyId);
            }
        } catch (e) {
            selectCompany.innerHTML = `<option value="">(Impossible de charger les entreprises)</option>`;
            console.error('Companies fetch error:', e);
        } finally {
            setLoading(selectCompany, false);
        }
    }

    // ===== CHARGEMENT ÉQUIPES =====
    async function fetchTeams(companyId = null) {
        if (!selectTeam) return;
        try {
            setLoading(selectTeam, true);
            let teams = [];
            if (role === 'chef_equipe') {
                teams = await fetchJson('/api/my-teams');
            } else if (companyId) {
                teams = await fetchJson(`/admin/teams?company_id=${encodeURIComponent(companyId)}`);
            }
            selectTeam.innerHTML = `<option value="">— Sélectionner une équipe —</option>` +
                (teams || []).map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            if (savedTeamId) selectTeam.value = savedTeamId;
        } catch (e) {
            selectTeam.innerHTML = `<option value="">(Impossible de charger les équipes)</option>`;
            console.error('Teams fetch error:', e);
        } finally {
            setLoading(selectTeam, false);
        }
    }

    // ===== EVENTS =====
    selectCompany?.addEventListener('change', async (e) => {
        const id = e.target.value || '';
        localStorage.setItem('selectedCompanyId', id);
        localStorage.removeItem('selectedTeamId');
        await fetchTeams(id);
    });
    selectTeam?.addEventListener('change', (e) => {
        const id = e.target.value || '';
        localStorage.setItem('selectedTeamId', id);
    });

    // ===== INIT =====
    (async () => {
        // NOTE : ces endpoints doivent renvoyer du JSON côté back sinon on affiche un message d’erreur.
        if (role === 'superadmin' || role === 'admin') {
            await fetchCompanies();
        } else if (role === 'chef_equipe') {
            await fetchTeams();
        }
    })();
})();
</script>
@endpush
