<div class="dossier-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dossier RH — <span class="text-primary">Julien Dupont</span></h2>
        <button class="btn btn-export">
            <i class="fa-solid fa-file-export me-2"></i> Exporter le dossier
        </button>
    </div>

    {{-- INDICATEUR DE COMPLÉTUDE --}}
    <div class="card dossier-progress mb-4">
        <h5 class="fw-semibold mb-3">Complétude du dossier</h5>
        <div class="progress">
            <div class="bar" style="width: 86%;">86%</div>
        </div>
        <p class="mt-2 small">Documents manquants : Fiche de fonction</p>
    </div>

    {{-- TABLE DES DOCUMENTS --}}
    <div class="dossier-documents">
        <table>
            <thead>
                <tr>
                    <th>Type de document</th>
                    <th>Date dépôt</th>
                    <th>Statut</th>
                    <th>Commentaire</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Carte d’identité</td>
                    <td>10/10/2025</td>
                    <td><span class="status valide">Validé</span></td>
                    <td>RAS</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action download"><i class="fa-solid fa-download"></i></button>
                            <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                            <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Certificat médical</td>
                    <td>12/09/2025</td>
                    <td><span class="status en-attente">En attente</span></td>
                    <td>Validation par RH</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action valide"><i class="fa-solid fa-check"></i></button>
                            <button class="btn-action refuse"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Permis de conduire</td>
                    <td>—</td>
                    <td><span class="status manquant">Manquant</span></td>
                    <td>Non déposé</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action upload"><i class="fa-solid fa-upload"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
