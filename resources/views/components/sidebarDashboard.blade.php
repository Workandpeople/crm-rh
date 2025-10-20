<aside class="sidebar d-flex flex-column justify-content-between">
    <div class="p-4">
        {{-- Liens Super Admin --}}
        @if (Auth::user()->role === 'superadmin')
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Super-Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-role="superadmin" data-page="usersManagement" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Utilisateurs & Rôles</a></li>
                <li><a href="#" data-role="superadmin" data-page="societes" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Sociétés</a></li>
                <li><a href="#" data-role="superadmin" data-page="equipes" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Gestion des Employés / Équipes</a></li>
                {{-- <li><a href="#" data-page="logs-security" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Logs & Sécurité</a></li>
                <li><a href="#" data-page="stats" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Statistiques globales</a></li> --}}
                <li><a href="#" data-role="superadmin" data-page="parametresSysteme" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Paramètres système</a></li>
            </ul>
        </div>
        @endif

        {{-- Liens Admin --}}
        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-role="admin" data-page="ticketing" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Ticketing RH</a></li>
                <li><a href="#" data-role="admin" data-page="dossierRH" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Dossier RH</a></li>
                <li><a href="#" data-role="admin" data-page="documentsRH" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Documents RH</a></li>
                <li><a href="#" data-role="admin" data-page="conges" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Congés / absences </a></li>
                <li><a href="#" data-role="admin" data-page="calendrierRH" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Calendrier RH</a></li>
                <li><a href="#" data-role="admin" data-page="notesFrais" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Notes de frais</a></li>
                <li><a href="#" data-role="admin" data-page="fichesPaie" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Fiches de paie</a></li>
                <li><a href="#" data-role="admin" data-page="organigramme" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Organigramme</a></li>
                <li><a href="#" data-role="admin" data-page="entretiens" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Entretiens</a></li>
                <li><a href="#" data-role="admin" data-page="statsRH" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Statistiques RH</a></li>
                <li><a href="#" data-role="admin" data-page="actualites" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Blog des Actualités</a></li>
            </ul>
        </div>
        @endif

        {{-- Liens Employés --}}
        @if (Auth::user()->role === 'user' || Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
        <div>
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Employés</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-role="employe" data-page="profil" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mon profil</a></li>
                <li><a href="#" data-role="employe" data-page="ticketing" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes tickets</a></li>
                <li><a href="#" data-role="employe" data-page="dossierRH" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes documents</a></li>
                <li><a href="#" data-role="employe" data-page="conges" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes congés</a></li>
                <li><a href="#" data-role="employe" data-page="notesFrais" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes notes de frais</a></li>
                <li><a href="#" data-role="employe" data-page="fichesPaie" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes fiches de paie</a></li>
                <li><a href="#" data-role="employe" data-page="calendrier" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mon calendrier</a></li>
                <li><a href="#" data-role="employe" data-page="actualites" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Actualités</a></li>
            </ul>
        </div>
        @endif
    </div>
    {{-- Déconnexion --}}
    <div class="deconnexion p-3 border-top">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn w-100 fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
            </button>
        </form>
    </div>


    {{-- Script pour le chargement dynamique --}}
{{-- Script pour le chargement dynamique --}}
@push('js')
<script>
(function() {
    let isLoading = false;

    /**
     * Charge dynamiquement une page du dashboard selon le rôle
     * @param {string} role - superadmin | admin | employe
     * @param {string} page - nom du fichier Blade (sans .blade.php)
     */
    async function loadContent(role, page) {
        if (isLoading) return;
        isLoading = true;

        const contentDiv = document.getElementById('dashboardContent');
        contentDiv.innerHTML = `<p  p-3">Chargement...</p>`;

        try {
            const response = await fetch(`/dashboard/${role}/${page}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);

            const html = await response.text();

            contentDiv.innerHTML = html.trim().length
                ? html
                : `<p class="text-warning p-3">⚠️ Aucun contenu trouvé pour "${page}".</p>`;

        } catch (error) {
            contentDiv.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${error.message}</p>`;
        } finally {
            isLoading = false;
        }
    }

    // --- Attache les clics sur le sidebar ---
    document.addEventListener('DOMContentLoaded', () => {
        const sidebarLinks = document.querySelectorAll('.nav-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                const role = link.getAttribute('data-role'); // ✅ Rôle ajouté
                const page = link.getAttribute('data-page');

                // Style actif
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Charge la page
                loadContent(role, page);
            });
        });

        // Charge la page par défaut au chargement
        const defaultRole = document.body.dataset.role || 'employe';
        loadContent(defaultRole, 'profil');
    });
})();
</script>
@endpush

</aside>
