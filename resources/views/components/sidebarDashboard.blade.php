<aside class="sidebar d-flex flex-column justify-content-between">
    <div class="p-4">
        @php($roleName = Auth::user()->role->name ?? 'employe')

        {{-- === Super Admin === --}}
        @if ($roleName === 'superadmin')
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Super-Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="usersManagement" class="nav-link d-block py-2 px-3 rounded">Utilisateurs & rôles</a></li>
                <li><a href="#" data-page="societes" class="nav-link d-block py-2 px-3 rounded">Sociétés</a></li>
                <li><a href="#" data-page="equipes" class="nav-link d-block py-2 px-3 rounded">Gestion équipes</a></li>
                <li><a href="#" data-page="parametresSysteme" class="nav-link d-block py-2 px-3 rounded">Paramètres système</a></li>
            </ul>
        </div>
        @endif

        {{-- === Admin === --}}
        @if (in_array($roleName, ['admin', 'superadmin']))
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="ticketing" class="nav-link d-block py-2 px-3 rounded">Ticketing RH</a></li>
                <li><a href="#" data-page="dossierRH" class="nav-link d-block py-2 px-3 rounded">Dossier RH</a></li>
                <li><a href="#" data-page="documentsRH" class="nav-link d-block py-2 px-3 rounded">Documents RH</a></li>
                <li><a href="#" data-page="conges" class="nav-link d-block py-2 px-3 rounded">Congés / absences</a></li>
                <li><a href="#" data-page="calendrierRH" class="nav-link d-block py-2 px-3 rounded">Calendrier RH</a></li>
                <li><a href="#" data-page="notesFrais" class="nav-link d-block py-2 px-3 rounded">Notes de frais</a></li>
                <li><a href="#" data-page="fichesPaie" class="nav-link d-block py-2 px-3 rounded">Fiches de paie</a></li>
                <li><a href="#" data-page="organigramme" class="nav-link d-block py-2 px-3 rounded">Organigramme</a></li>
                <li><a href="#" data-page="entretiens" class="nav-link d-block py-2 px-3 rounded">Entretiens</a></li>
                <li><a href="#" data-page="statsRH" class="nav-link d-block py-2 px-3 rounded">Statistiques RH</a></li>
                <li><a href="#" data-page="actualites" class="nav-link d-block py-2 px-3 rounded">Blog & actualités</a></li>
            </ul>
        </div>
        @endif

        {{-- === Chef d’équipe & Employés === --}}
        @if (in_array($roleName, ['employe', 'chef_equipe', 'admin', 'superadmin']))
        <div>
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Espace Employé</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="profil" class="nav-link d-block py-2 px-3 rounded">Mon profil</a></li>
                <li><a href="#" data-page="ticketing" class="nav-link d-block py-2 px-3 rounded">Mes tickets</a></li>
                <li><a href="#" data-page="dossierRH" class="nav-link d-block py-2 px-3 rounded">Mes documents</a></li>
                <li><a href="#" data-page="conges" class="nav-link d-block py-2 px-3 rounded">Mes congés</a></li>
                <li><a href="#" data-page="notesFrais" class="nav-link d-block py-2 px-3 rounded">Notes de frais</a></li>
                <li><a href="#" data-page="fichesPaie" class="nav-link d-block py-2 px-3 rounded">Fiches de paie</a></li>
                <li><a href="#" data-page="calendrier" class="nav-link d-block py-2 px-3 rounded">Calendrier</a></li>
                <li><a href="#" data-page="actualites" class="nav-link d-block py-2 px-3 rounded">Actualités</a></li>
            </ul>
        </div>
        @endif
    </div>

    {{-- === Déconnexion === --}}
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
    @include('components.sidebarContent.superadmin.userModals')
    @include('components.sidebarContent.superadmin.companyModals')
@endpush


@push('js')
<script>
(function() {
    let isLoading = false;
    const DEFAULT_PAGE = 'profil';
    const storageKey = 'dashboard:lastPage:{{ Auth::id() ?? 'guest' }}';

    function rememberPage(page) {
        try {
            window.localStorage.setItem(storageKey, page);
        } catch (error) {
            console.warn('[sidebar] Impossible de sauvegarder la page active', error);
        }
    }

    function readStoredPage() {
        try {
            return window.localStorage.getItem(storageKey);
        } catch (error) {
            console.warn('[sidebar] Impossible de lire la page sauvegardée', error);
            return null;
        }
    }

    async function loadContent(page) {
        if (isLoading) return;
        isLoading = true;

        const contentDiv = document.getElementById('dashboardContent');
        contentDiv.innerHTML = `<p class="p-3">Chargement...</p>`;

        try {
            const response = await fetch(`/dashboard/${page}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);

            const html = await response.text();
            contentDiv.innerHTML = html.trim().length
                ? html
                : `<p class="text-warning p-3">Aucun contenu trouvé pour "${page}".</p>`;

            rememberPage(page);

            // --- Nouvelle logique ---
            const scriptKey = contentDiv.querySelector('[data-script]')?.dataset.script;
            if (scriptKey && window.pageScripts?.[scriptKey]) {
                console.log(`[sidebar] Initialisation du script "${scriptKey}"`);
                window.pageScripts[scriptKey]();
            }
        } catch (error) {
            console.error('[sidebar] Erreur loadContent():', error);
            contentDiv.innerHTML = `<p class="text-danger p-3">Erreur : ${error.message}</p>`;
        } finally {
            isLoading = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('.nav-link');
        const setActiveLink = (page) => {
            links.forEach(link => {
                if (link.dataset.page === page) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        };

        links.forEach(link => link.addEventListener('click', e => {
            e.preventDefault();
            const targetPage = link.dataset.page;
            setActiveLink(targetPage);
            loadContent(targetPage);
        }));

        const initialPage = readStoredPage() || DEFAULT_PAGE;
        setActiveLink(initialPage);
        loadContent(initialPage);
    });
})();
</script>
@endpush
