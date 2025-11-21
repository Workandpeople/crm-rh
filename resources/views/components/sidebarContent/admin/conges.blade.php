<div class="conges-admin-page" data-script="leavesManagement"> {{-- CONGES = LEAVES --}}
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestion des congés</h2>
        {{-- ON remettra les boutons ici (export, etc.) --}}
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="conge-stats mb-4">
        <div class="stat-card">
            <h6>Total demandes</h6>
            <p>–</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>–</p>
        </div>
        <div class="stat-card">
            <h6>Validées</h6>
            <p>–</p>
        </div>
        <div class="stat-card">
            <h6>Refusées</h6>
            <p>–</p>
        </div>
    </div>

    {{-- FILTRES AVANCÉS --}}
    <div class="conge-filters mb-4 filters">
        {{-- Employé --}}
        <div class="fg">
            <label for="filter-employee">Employé</label>
            <select id="filter-employee" class="select">
                <option value="">Tous</option>
                {{-- options injectées en JS à partir des congés chargés --}}
            </select>
        </div>

        {{-- Type de congé --}}
        <div class="fg">
            <label for="filter-type">Type de congé</label>
            <select id="filter-type" class="select">
                <option value="">Tous</option>
                <option value="CP">Congés payés</option>
                <option value="SansSolde">Sans solde</option>
                <option value="Exceptionnel">Absence exceptionnelle</option>
                <option value="Maladie">Maladie</option>
            </select>
        </div>

        {{-- Période --}}
        <div class="fg">
            <label for="filter-start">Période</label>
            <div class="d-flex align-items-center gap-2">
                <input type="date" id="filter-start" class="input">
                <span style="opacity: .7;">→</span>
                <input type="date" id="filter-end" class="input">
            </div>
        </div>

        {{-- Reset --}}
        <div class="fg">
            <label>&nbsp;</label>
            <button type="button" id="btnLeavesReset" class="btn-reset-filters">
                Réinitialiser
            </button>
        </div>
    </div>


    {{-- TABLEAU DES DEMANDES (rempli en JS) --}}
    <div class="conge-table">
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="leavesTableBody">
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        Chargement des congés...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
