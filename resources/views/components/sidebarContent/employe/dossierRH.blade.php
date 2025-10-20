<div class="dossier-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mes Documents</h2>
        <button class="btn btn-upload-doc">
            <i class="fa-solid fa-upload me-2"></i> Ajouter un document
        </button>
    </div>

    {{-- INDICATEUR DE COMPLÉTUDE --}}
    <div class="card dossier-progress mb-4">
        <h5 class="fw-semibold mb-3">Complétude du dossier</h5>
        <div class="progress">
            <div class="bar" style="width: 78%;">78%</div>
        </div>
        <p class="mt-2 small text-white">Documents manquants : CNI, Attestation médicale</p>
    </div>

    {{-- LISTE DES DOCUMENTS --}}
    <div class="documents-list">
        <div class="document-card uploaded">
            <div class="doc-icon"><i class="fa-solid fa-id-card"></i></div>
            <div class="doc-info">
                <h6>Carte d’identité</h6>
                <p>Déposé le 10/08/2025</p>
            </div>
            <div class="doc-actions">
                <button class="btn-action"><i class="fa-solid fa-download"></i></button>
                <button class="btn-action delete"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>

        <div class="document-card missing">
            <div class="doc-icon"><i class="fa-solid fa-file-medical"></i></div>
            <div class="doc-info">
                <h6>Certificat médical</h6>
                <p>Non déposé</p>
            </div>
            <div class="doc-actions">
                <button class="btn-action upload"><i class="fa-solid fa-upload"></i></button>
            </div>
        </div>

        <div class="document-card expired">
            <div class="doc-icon"><i class="fa-solid fa-id-badge"></i></div>
            <div class="doc-info">
                <h6>Permis de conduire</h6>
                <p>Expiré le 12/09/2025</p>
            </div>
            <div class="doc-actions">
                <button class="btn-action upload"><i class="fa-solid fa-upload"></i></button>
            </div>
        </div>
    </div>
</div>
