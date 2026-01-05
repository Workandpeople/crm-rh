@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage;
    use App\Models\Ticket;
    $user = auth()->user();
    $userFullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    $docs = $user->documents?->keyBy('type') ?? collect();
    $ticketByDocType = Ticket::where('type', 'document_rh')
        ->where('created_by', $user->id)
        ->orderByDesc('created_at')
        ->get()
        ->mapWithKeys(fn($t) => [($t->details['doc_type'] ?? '') => $t]);
    $requiredDocs = [
        'contrat' => 'Contrat / Avenants',
        'cni' => 'CNI / Photo',
        'carte_vitale' => 'Carte vitale',
        'permis' => 'Permis de conduire',
        'carte_grise' => 'Carte grise (CEE)',
        'fiche_fonction' => 'Fiche de fonction',
        'fiche_epi' => 'Fiche remise EPI',
        'charte_deontologique' => 'Charte déontologique',
        'rgpd' => 'RGPD',
        'habilitation' => 'Habilitations',
        'diplome' => 'Diplômes',
        'iso_17020' => 'ISO 17020',
        'iso_9001' => 'ISO 9001',
        'iso_26000' => 'ISO 26000',
        'formation_integration' => 'Formation intégration',
        'formation_routiere' => 'Sensibilisation routière',
        'formation_groupe' => 'Formation groupe',
        'formation_tutorat' => 'Formation tutorat',
        'qcm' => 'QCM / tests site',
        'certificat' => 'Certificats',
        'supervision' => 'Supervision',
        'cv' => 'CV',
    ];

    $statusIcons = [
        'accepted' => 'fa-check-circle',
        'pending' => 'fa-clock',
        'refused' => 'fa-triangle-exclamation',
        'missing' => 'fa-ban',
    ];

    $computed = collect($requiredDocs)->map(function ($label, $type) use ($docs, $ticketByDocType) {
        $doc = $docs->get($type);
        $ticket = $ticketByDocType[$type] ?? null;
        if (! $doc) {
            return [
                'type' => $type,
                'label' => $label,
                'state' => 'missing',
                'text' => 'Non déposé',
                'file_path' => null,
                'status' => null,
                'id' => null,
                'file_url' => null,
            ];
        }

        $isExpired = $doc->expires_at && Carbon::parse($doc->expires_at)->isPast();
        $status = $doc->status;
        // Priorité au ticket le plus récent pour afficher l'état courant
        if ($ticket) {
            $status = $ticket->status === 'valide'
                ? 'valid'
                : ($ticket->status === 'refuse' ? 'rejected' : $status);
        }
        $state = match (true) {
            $isExpired => 'refused',
            $status === 'valid' => 'accepted',
            $status === 'rejected' => 'refused',
            $status === 'pending' => 'pending',
            default => 'pending',
        };

        $textParts = [];
        if ($doc->uploaded_at) {
            $textParts[] = 'Déposé le ' . Carbon::parse($doc->uploaded_at)->format('d/m/Y');
        }
        if ($doc->expires_at) {
            $textParts[] = 'Expire le ' . Carbon::parse($doc->expires_at)->format('d/m/Y');
        }

        return [
            'type' => $type,
            'label' => $label,
            'state' => $state,
            'status'=> $status,
            'text' => implode(' — ', $textParts) ?: 'Déposé',
            'file_path' => $doc->file_path,
            'file_url' => $doc->file_path ? Storage::url($doc->file_path) : null,
            'id' => $doc->id,
        ];
    });

    $validCount = $computed->where('state', 'accepted')->count();
    $completion = round(($validCount / max(count($requiredDocs), 1)) * 100);
    $missingLabels = $computed
        ->filter(fn ($doc) => in_array($doc['state'], ['missing', 'pending', 'refused']))
        ->pluck('label')
        ->implode(', ');
    $progressClass = match (true) {
        $completion < 40 => 'progress-low',
        $completion < 70 => 'progress-medium',
        default => 'progress-high',
    };
@endphp

<div class="dossier-page" data-script="dossierEmployee" data-user-name="{{ $userFullName }}">
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
            <div class="bar {{ $progressClass }}" style="width: {{ $completion }}%;">
                {{ $completion }}%
            </div>
        </div>
        <p class="mt-2 small text-white">
            Documents manquants : {{ $missingLabels ?: 'Aucun (tous déposés)' }}
        </p>
    </div>

    {{-- LISTE DES DOCUMENTS --}}
    <div class="documents-list">
        @foreach ($computed as $doc)
            @php
                $stateClass = match ($doc['state']) {
                    'accepted' => 'accepted',
                    'pending' => 'pending',
                    'refused' => 'refused',
                    default => 'missing',
                };
                $icon = $statusIcons[$doc['state']] ?? 'fa-file';
            @endphp
            <div class="document-card {{ $stateClass }}">
                <div class="doc-icon"><i class="fa-solid {{ $icon }}"></i></div>
                <div class="doc-info">
                    <h6>{{ $doc['label'] }}</h6>
                    <p>{{ $doc['text'] }}</p>
                </div>
                <div class="doc-actions">
                    @if (!empty($doc['file_url']))
                        <a class="btn-action" href="{{ $doc['file_url'] }}" target="_blank" rel="noopener">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    @endif

                    @if (in_array($doc['state'], ['pending', 'accepted']) && !empty($doc['file_url']))
                        <a class="btn-action" href="{{ $doc['file_url'] }}" download>
                            <i class="fa-solid fa-download"></i>
                        </a>
                    @endif

                    @if ($doc['state'] === 'pending' && $doc['id'])
                        <button class="btn-action cancel" data-doc-id="{{ $doc['id'] }}" data-doc-type="{{ $doc['type'] }}">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    @endif

                    @if (in_array($doc['state'], ['missing', 'refused']))
                        <button class="btn-action upload" data-doc-type="{{ $doc['type'] }}" data-doc-label="{{ $doc['label'] }}" data-lock-type="1">
                            <i class="fa-solid fa-upload"></i>
                        </button>
                    @elseif ($doc['state'] === 'pending')
                        {{-- en attente : pas de nouveau dépôt --}}
                    @elseif ($doc['state'] === 'accepted')
                        {{-- accepté : pas de suppression ni ré-upload --}}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- MODALE DÉPÔT DE DOCUMENT (crée un ticket "document_rh") --}}
<div class="modal fade" id="modalUploadDoc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUploadDoc" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="company_id" value="{{ $user->company_id }}">
                <input type="hidden" name="type" value="document_rh">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Déposer un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type de document</label>
                        <select name="doc_type" id="docTypeSelect" class="form-select" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($requiredDocs as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fichier</label>
                        <input type="file" name="doc_file" id="docFileInput" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Titre du ticket</label>
                        <input type="text" name="title" id="docTitleInput" class="form-control" required placeholder="Ex : Dépôt {{ $requiredDocs['contrat'] }} - {{ $userFullName }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="docDescInput" class="form-control" rows="4" placeholder="Informations complémentaires ou date d'expiration du document."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Déposer le document</button>
                </div>
            </form>
        </div>
    </div>
</div>
