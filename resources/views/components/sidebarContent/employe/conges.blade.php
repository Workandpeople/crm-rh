<div class="conges-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mes congés</h2>
        <button class="btn btn-new-conge">
            <i class="fa-solid fa-plane-departure me-2"></i> Nouvelle demande
        </button>
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="conge-stats mb-4">
        <div class="stat-card">
            <h6>Congés restants</h6>
            <p>10 jours</p>
        </div>
        <div class="stat-card">
            <h6>Congés pris</h6>
            <p>15 jours</p>
        </div>
        <div class="stat-card">
            <h6>En attente</h6>
            <p>2 demandes</p>
        </div>
    </div>

    {{-- TABLEAU DES DEMANDES --}}
    <div class="conge-table">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Congé payé</td>
                    <td>15/11/2025 → 20/11/2025</td>
                    <td>5 jours</td>
                    <td><span class="status en-attente">En attente</span></td>
                    <td><button class="btn-action">Détails</button></td>
                </tr>
                <tr>
                    <td>RTT</td>
                    <td>05/09/2025 → 06/09/2025</td>
                    <td>2 jours</td>
                    <td><span class="status valide">Validé</span></td>
                    <td><button class="btn-action">Détails</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
