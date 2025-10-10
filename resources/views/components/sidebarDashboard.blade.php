<aside class="sidebar d-flex flex-column justify-content-between">
    <div class="p-4">
        {{-- Liens Super Admin --}}
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Super-Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="comptes" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Gestion des comptes</a></li>
                <li><a href="#" data-page="param-system" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Paramètres système</a></li>
                <li><a href="#" data-page="logs-security" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Logs & Sécurité</a></li>
            </ul>
        </div>

        {{-- Liens Admin --}}
        <div class="mb-4">
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Admin</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="employes" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Employés</a></li>
                <li><a href="#" data-page="candidatures" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Candidatures</a></li>
                <li><a href="#" data-page="conges-rh" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Congés</a></li>
            </ul>
        </div>

        {{-- Liens Employés --}}
        <div>
            <h6 class="text-uppercase small fw-bold mb-2 section-title">Pages Employés</h6>
            <ul class="list-unstyled mb-0">
                <li><a href="#" data-page="mainDashboard" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mon profil</a></li>
                <li><a href="#" data-page="conges-employes" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Mes congés</a></li>
                <li><a href="#" data-page="evaluations" class="nav-link d-block py-2 px-3 rounded text-decoration-none">Évaluations</a></li>
            </ul>
        </div>
    </div>

    {{-- Déconnexion --}}
    <div class="p-3 border-top logout">
        <button class="btn w-100 fw-semibold">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion
        </button>
    </div>

    {{-- Script pour le chargement dynamique --}}
    @push('js')
        <script>
        (function() {
            let isLoading = false; // Empêche les requêtes multiples

            async function loadContent(page) {
                if (isLoading) return; // évite le spam de clics rapides
                isLoading = true;

                const contentDiv = document.getElementById('dashboardContent');
                contentDiv.innerHTML = `<p>Chargement...</p>`;

                try {
                    const response = await fetch(`/dashboard/${page}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) {
                        throw new Error(`Erreur HTTP ${response.status}`);
                    }

                    const html = await response.text();

                    // Vérifie que la réponse contient bien du HTML
                    if (html.trim().length === 0) {
                        contentDiv.innerHTML = `<p class="text-warning">⚠️ Aucun contenu trouvé pour "${page}".</p>`;
                    } else {
                        contentDiv.innerHTML = html;
                    }
                } catch (error) {
                    contentDiv.innerHTML = `<p class="text-danger">Erreur de chargement : ${error.message}</p>`;
                } finally {
                    isLoading = false;
                }
            }

            // Attache les clics de manière fiable
            document.addEventListener('DOMContentLoaded', () => {
                const sidebarLinks = document.querySelectorAll('.nav-link');

                sidebarLinks.forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const page = link.getAttribute('data-page');

                        // Retire le style actif précédent
                        sidebarLinks.forEach(l => l.classList.remove('active'));
                        link.classList.add('active');

                        // Charge la page demandée
                        loadContent(page);
                    });
                });

                // Chargement initial (page principale)
                loadContent('mainDashboard');
            });
        })();
        </script>
    @endpush

</aside>
