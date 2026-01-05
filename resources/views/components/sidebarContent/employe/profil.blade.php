<div class="profil-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mon Profil</h2>
        <button class="btn btn-edit-profile">
            <i class="fa-solid fa-pen me-2"></i> Modifier mon profil
        </button>
    </div>

    @php
        use Carbon\Carbon;
        use App\Models\Ticket;

        $user = auth()->user();
        $profile = $user->profile;
        $company = $user->company;
        $team = $user->team;
        $display = function ($value, $hasField = true) {
            if (! $hasField) {
                return 'not in bdd';
            }
            if (is_null($value) || $value === '') {
                return 'to be defined';
            }
            return $value;
        };

        $docs = $user->documents?->keyBy('type') ?? collect();
        $ticketByDocType = Ticket::where('type', 'document_rh')
            ->where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn ($t) => [($t->details['doc_type'] ?? '') => $t]);

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

        $docStates = collect($requiredDocs)->map(function ($label, $type) use ($docs, $ticketByDocType) {
            $doc = $docs->get($type);
            $ticket = $ticketByDocType[$type] ?? null;

            if (! $doc) {
                return [
                    'label' => $label,
                    'state' => 'missing',
                ];
            }

            $isExpired = $doc->expires_at && Carbon::parse($doc->expires_at)->isPast();
            $status = $doc->status;

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

            return [
                'label' => $label,
                'state' => $state,
            ];
        });

        $validCount = $docStates->where('state', 'accepted')->count();
        $completion = round(($validCount / max(count($requiredDocs), 1)) * 100);
        $missingLabels = $docStates
            ->filter(fn ($doc) => in_array($doc['state'], ['missing', 'pending', 'refused']))
            ->pluck('label')
            ->implode(', ');
        $progressClass = match (true) {
            $completion < 40 => 'progress-low',
            $completion < 70 => 'progress-medium',
            default => 'progress-high',
        };
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <div class="profil-avatar">
                    <img src="{{ asset('images/avatar.png') }}" alt="Avatar">
                    <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p>{{ $user->email }}</p>
                    <span class="badge mt-3">{{ ucfirst($display($user->status)) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="fw-semibold mb-3">Informations personnelles</h5>
                <div class="profil-info">
                    <div class="profil-info-item">
                        <label>Nom complet</label>
                        <p>{{ $display(trim($user->first_name.' '.$user->last_name)) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Adresse e-mail</label>
                        <p>{{ $display($user->email) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Poste</label>
                        <p>{{ $display($profile->position ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Société</label>
                        <p>{{ $display($company->name ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Équipe</label>
                        <p>{{ $display($team->name ?? null) }}</p>
                    </div>
                    <div class="profil-info-item">
                        <label>Date d'embauche</label>
                        <p>{{ $display(optional($profile)->hire_date?->format('d/m/Y')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <h5 class="fw-semibold mb-3">Complétude du dossier RH</h5>
        <div class="profil-progress">
            <div class="bar {{ $progressClass }}" style="width: {{ $completion }}%;">
                {{ $completion }}%
            </div>
        </div>
        <p class="mt-2 small" style="color: var(--color-text-muted);">
            Documents manquants : {{ $missingLabels ?: 'Aucun (tous déposés)' }}
            @if ($missingLabels)
                — <a href="#" class="link-dynamic fw-semibold" data-page="dossierRH">Compléter maintenant</a>
            @endif
        </p>
    </div>

    <div class="profil-actions">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-folder-open"></i>
                <h6 class="mb-0">Voir mon dossier RH</h6>
            </div>
            <a href="{{ route('dashboard.page', ['page' => 'dossierRH']) }}" class="link-dynamic" data-page="dossierRH">Ouvrir</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-plane-departure"></i>
                <h6 class="mb-0">Faire une demande de congé</h6>
            </div>
            <a href="#" class="link-dynamic" data-page="ticketing">Accéder</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-ticket"></i>
                <h6 class="mb-0">Ouvrir un ticket RH</h6>
            </div>
            <a href="#" class="link-dynamic" data-page="ticketing">Créer</a>
        </div>
        <div class="card p-4">
            <div class="d-flex align-items-center gap-2 justify-content-center mb-2">
                <i class="fa-solid fa-receipt"></i>
                <h6 class="mb-0">Fiche de Paie</h6>
            </div>
            <a href="https://monespacepaye.example.com" target="_blank" rel="noopener" class="link-dynamic">Consulter</a>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('click', (e) => {
            const target = e.target.closest('.link-dynamic');
            if (target) {
                e.preventDefault();
                loadContent(target.dataset.page);
            }
        });
    </script>
@endpush
