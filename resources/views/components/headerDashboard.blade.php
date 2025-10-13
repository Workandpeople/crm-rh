<header class="d-flex align-items-center justify-content-between px-4 py-3"
        style="background-color: var(--color-bg-secondary); border-bottom: 1px solid var(--color-border); position: fixed; top: 0; left: 0; right: 0; z-index: 1000;">

    {{-- Logo à gauche --}}
    <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 38px;">
        <h4 class="fw-bold mb-0" style="color: var(--color-text);">CRM RH</h4>
    </div>

    {{-- Profil utilisateur --}}
    <div class="d-flex align-items-center gap-3" id="profileMenuToggle" style="cursor: pointer;">
        <div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-primary);">
            <img src="{{ asset('images/avatar.png') }}" alt="Profil" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <span style="color: var(--color-text); font-weight: 600;">Nom Prénom</span>
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

    @push('js')
    <script>
        const toggleBtn = document.getElementById('profileMenuToggle');
        const dropdown = document.getElementById('profileDropdown');
        toggleBtn.addEventListener('click', () => {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', (e) => {
            if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
    @endpush
</header>
