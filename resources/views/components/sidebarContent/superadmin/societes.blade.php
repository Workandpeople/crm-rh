<div class="societes-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des sociétés</h2>
        <button class="btn btn-new-societe">
            <i class="fa-solid fa-building-circle-plus me-2"></i> Nouvelle société
        </button>
    </div>

    {{-- LISTE DES SOCIÉTÉS --}}
    <div class="societes-list">
        <div class="societe-card">
            <div class="societe-header d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/genius-logo.png') }}" alt="Logo Genius Contrôle" class="societe-logo">
                    <div>
                        <h4 class="mb-1">Genius Contrôle</h4>
                        <p class="text-muted mb-0">Société active</p>
                    </div>
                </div>
                <span class="status active">Active</span>
            </div>

            <div class="societe-info">
                <p><i class="fa-solid fa-envelope me-2 text-primary"></i> contact@geniuscontrole.fr</p>
                <p><i class="fa-solid fa-phone me-2 text-primary"></i> +33 1 45 62 89 12</p>
                <p><i class="fa-solid fa-location-dot me-2 text-primary"></i> 12 Rue de l’Industrie, 75012 Paris</p>
            </div>

            <div class="societe-footer d-flex justify-content-between align-items-center mt-3">
                <div class="admin-info">
                    <small>Administrateur : <strong>David</strong></small>
                </div>
                <div class="actions">
                    <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
        </div>

        <div class="societe-card">
            <div class="societe-header d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/workandpeople-logo.png') }}" alt="Logo Work and People" class="societe-logo">
                    <div>
                        <h4 class="mb-1">Work and People</h4>
                        <p class="text-muted mb-0">Société active</p>
                    </div>
                </div>
                <span class="status active">Active</span>
            </div>

            <div class="societe-info">
                <p><i class="fa-solid fa-envelope me-2 text-primary"></i> contact@workandpeople.fr</p>
                <p><i class="fa-solid fa-phone me-2 text-primary"></i> +33 1 72 43 19 80</p>
                <p><i class="fa-solid fa-location-dot me-2 text-primary"></i> 8 Avenue de la Défense, 92000 Nanterre</p>
            </div>

            <div class="societe-footer d-flex justify-content-between align-items-center mt-3">
                <div class="admin-info">
                    <small>Administrateur : <strong>Alex</strong></small>
                </div>
                <div class="actions">
                    <button class="btn-action edit"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
