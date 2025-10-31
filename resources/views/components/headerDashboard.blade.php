<header class="d-flex align-items-center justify-content-between px-4 py-3"
        style="background-color: var(--color-bg-secondary); border-bottom: 1px solid var(--color-border); position: fixed; top: 0; left: 0; right: 0; z-index: 1000;">

    {{-- === GAUCHE : Logo + Sélecteurs === --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Logo --}}
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 38px;">
            <h4 class="fw-bold mb-0" style="color: var(--color-text);">CRM RH</h4>
        </div>

        {{-- Sélecteurs dynamiques --}}
        <div id="selectorsArea" class="d-flex align-items-center gap-3 ms-4">
            @php
                $user = auth()->user();
                $role = $user->role?->name ?? '';
            @endphp

            {{-- Liste des entreprises (superadmin ou admin uniquement) --}}
            @if(in_array($role, ['superadmin', 'admin']))
                <select id="selectCompany" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="">-- Sélectionner une entreprise --</option>
                </select>
            @endif

            {{-- Liste des équipes (superadmin/admin : liée à l’entreprise choisie | chef d’équipe : ses équipes) --}}
            @if(in_array($role, ['superadmin', 'admin', 'chef_equipe']))
                <select id="selectTeam" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="">-- Sélectionner une équipe --</option>
                </select>
            @endif
        </div>
    </div>

    {{-- === DROITE : Profil utilisateur === --}}
    <div class="d-flex align-items-center gap-3" id="profileMenuToggle" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-primary);">
            <img src="{{ asset('images/avatar.png') }}" alt="Profil" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <span class="fw-bold mb-1">{{ auth()->user()->first_name ?? 'Utilisateur' }}</span>
    </div>

    {{-- Dropdown profil --}}
    <div id="profileDropdown"
         style="display: none; position: absolute; top: 70px; right: 20px; background-color: var(--color-bg-secondary); border: 1px solid var(--color-border); border-radius: 0.75rem; width: 220px;">
        <a href="#" class="d-block py-2 px-3 text-decoration-none" style="color: var(--color-text);">Mon profil</a>
        <a href="#" class="d-block py-2 px-3 text-decoration-none" style="color: var(--color-text);">Paramètres</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-100 text-start py-2 px-3 border-0 bg-transparent text-danger fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
            </button>
        </form>
    </div>
</header>

@push('js')
    <script>
        const role = @json($role);
        const selectCompany = document.getElementById('selectCompany');
        const selectTeam = document.getElementById('selectTeam');

        // Récupère la valeur en localStorage
        const savedCompanyId = localStorage.getItem('selectedCompanyId');
        const savedTeamId = localStorage.getItem('selectedTeamId');

        // === PROFIL DROPDOWN ===
        const toggleBtn = document.getElementById('profileMenuToggle');
        const dropdown = document.getElementById('profileDropdown');
        toggleBtn?.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
            if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // === FONCTIONS FETCH ===
        async function fetchCompanies() {
            const res = await fetch('/admin/companies', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();

            selectCompany.innerHTML = `<option value="">-- Sélectionner une entreprise --</option>` +
                data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

            if (savedCompanyId) {
                selectCompany.value = savedCompanyId;
                await fetchTeams(savedCompanyId);
            }
        }

        async function fetchTeams(companyId = null) {
            if (!selectTeam) return;
            selectTeam.innerHTML = `<option value="">-- Sélectionner une équipe --</option>`;

            if (role === 'chef_equipe') {
                const res = await fetch('/api/my-teams', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const teams = await res.json();
                selectTeam.innerHTML += teams.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            } else if (companyId) {
                const res = await fetch(`/admin/teams?company_id=${companyId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const teams = await res.json();
                selectTeam.innerHTML += teams.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
            }

            if (savedTeamId) selectTeam.value = savedTeamId;
        }

        // === GESTION DES CHANGEMENTS ===
        selectCompany?.addEventListener('change', async (e) => {
            const id = e.target.value;
            localStorage.setItem('selectedCompanyId', id);
            localStorage.removeItem('selectedTeamId');
            await fetchTeams(id);
        });

        selectTeam?.addEventListener('change', (e) => {
            const id = e.target.value;
            localStorage.setItem('selectedTeamId', id);
        });

        // === INIT ===
        (async () => {
            if (role === 'superadmin' || role === 'admin') {
                await fetchCompanies();
            } else if (role === 'chef_equipe') {
                await fetchTeams();
            }
        })();
    </script>
@endpush
