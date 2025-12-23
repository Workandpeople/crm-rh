<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Leave;
use App\Models\Expense;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BacklogController extends Controller
{
    /**
     * Liste des tickets (JSON) avec filtres.
     * - Admin / chef / superadmin : sur toute la société (ou company_id passé)
     * - Employé : uniquement ses tickets (créés ou assignés) + filtre mine=true possible
     */
   public function index(Request $request)
    {
        try {
            $user     = Auth::user();
            $roleName = $user->role->name ?? null;

            if (!in_array($roleName, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }

            $isEmployee = $roleName === 'employe';
            $onlyMine   = $request->boolean('mine', false) || $isEmployee;

            // --- paramètres reçus ---
            $companyId  = $isEmployee
                ? $user->company_id
                : $request->query('company_id', $user->company_id);

            $type       = $request->query('type');                 // conge, note_frais, ...
            $status     = $request->query('status');               // en_attente, valide, refuse
            $employeeId = $isEmployee ? null : $request->query('employee_id');
            $search     = $request->query('search');

            // --- filtres spécifiques ---
            $leaveStart       = $request->query('leave_start');        // YYYY-MM-DD
            $leaveEnd         = $request->query('leave_end');          // YYYY-MM-DD
            $expenseMin       = $request->query('expense_min');        // numeric
            $expenseMax       = $request->query('expense_max');        // numeric
            $documentType     = $request->query('document_type');      // string
            $incidentSeverity = $request->query('incident_severity');  // mineur|majeur|critique

            Log::info('[BacklogController@index] Filtres', [
                'user_id' => $user->id,
                'role' => $roleName,
                'company_id' => $companyId,
                'onlyMine' => $onlyMine,
                'type' => $type,
                'status' => $status,
                'employee_id' => $employeeId,
                'search' => $search,
                'leave_start' => $leaveStart,
                'leave_end' => $leaveEnd,
                'expense_min' => $expenseMin,
                'expense_max' => $expenseMax,
                'document_type' => $documentType,
                'incident_severity' => $incidentSeverity,
            ]);

            // =========================
            // Base query (on ne la "consomme" pas)
            // =========================
            $baseQuery = Ticket::query()
                ->with(['creator', 'assignee', 'company', 'relatedUser'])
                ->where('company_id', $companyId)
                ->when($onlyMine, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('created_by', $user->id)
                            ->orWhere('assigned_to', $user->id)
                            ->orWhere('related_user_id', $user->id);
                    });
                });

            // =========================
            // Filtres de base
            // =========================
            if ($type && $type !== 'all') {
                $baseQuery->where('type', $type);
            }

            if ($status) {
                $baseQuery->where('status', $status);
            }

            if ($employeeId) {
                $baseQuery->where(function ($q) use ($employeeId) {
                    $q->where('created_by', $employeeId)
                    ->orWhere('related_user_id', $employeeId);
                });
            }

            if ($search) {
                $s = '%' . trim($search) . '%';
                $baseQuery->where(function ($q) use ($s) {
                    $q->where('title', 'LIKE', $s)
                    ->orWhere('description', 'LIKE', $s)
                    ->orWhereHas('creator', function ($q2) use ($s) {
                        $q2->where('first_name', 'LIKE', $s)
                            ->orWhere('last_name', 'LIKE', $s)
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$s]);
                    })
                    ->orWhereHas('relatedUser', function ($q3) use ($s) {
                        $q3->where('first_name', 'LIKE', $s)
                            ->orWhere('last_name', 'LIKE', $s)
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$s]);
                    });
                });
            }

            // =========================
            // Filtres spécifiques par type
            // =========================

            // Congés : overlap sur période demandée
            if ($leaveStart || $leaveEnd) {
                $startDate = $leaveStart ?: '1900-01-01';
                $endDate   = $leaveEnd   ?: '2999-12-31';

                // si aucun type n'est fixé, on force conge
                if (!$type || $type === 'all') {
                    $baseQuery->where('type', 'conge');
                }

                $baseQuery->where(function ($q) use ($startDate, $endDate) {
                    // overlap : start <= endFilter AND end >= startFilter
                    $q->whereDate('leave_start_date', '<=', $endDate)
                    ->whereDate('leave_end_date', '>=', $startDate);
                });
            }

            // Notes de frais : montant min/max
            if (($expenseMin !== null && $expenseMin !== '') || ($expenseMax !== null && $expenseMax !== '')) {
                if (!$type || $type === 'all') {
                    $baseQuery->where('type', 'note_frais');
                }

                if ($expenseMin !== null && $expenseMin !== '') {
                    $baseQuery->where('expense_amount', '>=', (float) $expenseMin);
                }
                if ($expenseMax !== null && $expenseMax !== '') {
                    $baseQuery->where('expense_amount', '<=', (float) $expenseMax);
                }
            }

            // Documents RH : type document
            if ($documentType) {
                if (!$type || $type === 'all') {
                    $baseQuery->where('type', 'document_rh');
                }
                $baseQuery->where('document_type', $documentType);
            }

            // Incidents : gravité
            if ($incidentSeverity) {
                if (!$type || $type === 'all') {
                    $baseQuery->where('type', 'incident');
                }
                $baseQuery->where('incident_severity', $incidentSeverity);
            }

            // =========================
            // Liste
            // =========================
            $listQuery = (clone $baseQuery)->latest('created_at');
            $tickets = $listQuery->get();

            $tickets->each(function ($t) {
                if ($t->creator) {
                    $t->creator->full_name = trim(($t->creator->first_name ?? '') . ' ' . ($t->creator->last_name ?? ''));
                }
                if ($t->assignee) {
                    $t->assignee->full_name = trim(($t->assignee->first_name ?? '') . ' ' . ($t->assignee->last_name ?? ''));
                }
                if ($t->relatedUser) {
                    $t->relatedUser->full_name = trim(($t->relatedUser->first_name ?? '') . ' ' . ($t->relatedUser->last_name ?? ''));
                }
            });

            // =========================
            // Stats (sur la même base que la liste)
            // =========================
            $statsBase = clone $baseQuery;

            $stats = [
                'total'     => (clone $statsBase)->count(),
                'pending'   => (clone $statsBase)->where('status', 'en_attente')->count(),
                'validated' => (clone $statsBase)->where('status', 'valide')->count(),
                'refused'   => (clone $statsBase)->where('status', 'refuse')->count(),
            ];

            return response()->json([
                'tickets' => $tickets,
                'stats'   => $stats,
            ]);
        } catch (\Throwable $e) {
            Log::error('[BacklogController@index] Erreur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => 'Erreur serveur',
            ], 500);
        }
    }



    /**
     * Utilisateurs pouvant être assignés / concernés.
     * → Retourne un seul tableau "users" + alias assignees/related_users pour compat JS.
     */
    public function options(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (!in_array($role, ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $companyId = $request->query('company_id', $user->company_id);

        $users = User::where('company_id', $companyId)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $users->each(function ($u) {
            $u->full_name = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
        });

        return response()->json([
            'users'         => $users,
            'assignees'     => $users,
            'related_users' => $users,
        ]);
    }

    /**
     * Création d'un ticket + entité métier associée (congé, note de frais, document RH, etc.).
     */
    public function store(Request $request){
    $user = Auth::user();
    $role = $user->role->name ?? null;

    if (!in_array($role, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

        if ($role === 'employe' && $user?->company_id) {
            $request->merge(['company_id' => $user->company_id]);
        }


        // Validation (on supporte à la fois "document_type" et un éventuel "doc_type")
        $validated = $request->validate([
        'title'           => 'required|string|max:255',
        'type'            => 'required|in:conge,note_frais,incident,document_rh,autre',
        'description'     => 'nullable|string',

        // ✅ si employé : il ne doit pas assigner quelqu’un
        // (sinon tu peux laisser, mais c’est une porte)
        'assignee_id'     => $role === 'employe' ? 'nullable' : 'nullable|exists:users,id',

        'priority'        => 'nullable|in:basse,moyenne,haute',
        'due_date'        => 'nullable|date',
        'related_user_id' => 'nullable|exists:users,id',
        'company_id'      => 'required|exists:companies,id',

        'leave_type'       => 'nullable|in:CP,SansSolde,Exceptionnel,Maladie',
        'leave_start_date' => 'nullable|date',
        'leave_end_date'   => 'nullable|date|after_or_equal:leave_start_date',

        'expense_type'   => 'nullable|in:repas,peage,hebergement,km',
        'expense_amount' => 'nullable|numeric|min:0',
        'expense_date'   => 'nullable|date',

        'document_type'        => 'nullable|string|max:255',
        'document_expires_at'  => 'nullable|date',

        'doc_type'        => 'nullable|string|max:255',
        'doc_file'        => 'nullable|file|max:5120',

        'incident_severity' => 'nullable|in:mineur,majeur,critique',
    ]);

        // DocType final : on prend d'abord document_type (modale actuelle), sinon doc_type
        $docType = $validated['document_type'] ?? $validated['doc_type'] ?? null;

        // Si type document_rh, le docType est obligatoire
        if (($validated['type'] ?? null) === 'document_rh' && empty($docType)) {
            return response()->json([
                'message' => 'Type de document requis pour un ticket Document RH.',
            ], 422);
        }

        // Un employé ne peut créer que dans SA société
        if ($role === 'employe') {
            $validated['company_id'] = $user->company_id;
        }

        // Employé concerné : celui choisi, sinon le créateur
        $targetUserId = $validated['related_user_id'] ?? $user->id;

        try {
            DB::beginTransaction();

            // Si document_rh : on supprime un éventuel ancien ticket refusé pour le même docType
            if (($validated['type'] ?? null) === 'document_rh' && $docType) {
                Ticket::where('type', 'document_rh')
                    ->where('created_by', $user->id)
                    ->where('status', 'refuse')
                    ->where('document_type', $docType)
                    ->delete();
            }

            // 1) Création du ticket
            $ticket = Ticket::create([
                'company_id'          => $validated['company_id'],
                'created_by'          => $user->id,
                'assigned_to'         => $validated['assignee_id'] ?? null,
                'type'                => $validated['type'],
                'title'               => $validated['title'],
                'description'         => $validated['description'] ?? '',
                'priority'            => $validated['priority'] ?? 'moyenne',
                'status'              => 'en_attente',
                'due_date'            => $validated['due_date'] ?? null,
                'related_user_id'     => $validated['related_user_id'] ?? null,

                // champs spécifiques (nullable) pour conge / note_frais / document_rh / incident
                'leave_type'          => $validated['leave_type'] ?? null,
                'leave_start_date'    => $validated['leave_start_date'] ?? null,
                'leave_end_date'      => $validated['leave_end_date'] ?? null,

                'expense_type'        => $validated['expense_type'] ?? null,
                'expense_amount'      => $validated['expense_amount'] ?? null,
                'expense_date'        => $validated['expense_date'] ?? null,

                'document_type'       => $docType,
                'document_expires_at' => $validated['document_expires_at'] ?? null,

                'incident_severity'   => $validated['incident_severity'] ?? null,
            ]);

            // 2) Entité métier associée
            switch ($ticket->type) {
                case 'conge':
                    if (!empty($validated['leave_start_date']) && !empty($validated['leave_end_date'])) {
                        Leave::create([
                            'user_id'            => $targetUserId,
                            'type'               => $validated['leave_type'] ?? 'CP',
                            'start_date'         => $validated['leave_start_date'],
                            'end_date'           => $validated['leave_end_date'],
                            'justification_path' => null,
                            'status'             => 'pending',
                            'validated_by'       => null,
                            'comments'           => $validated['description'] ?? $validated['title'],
                        ]);
                    }
                    break;

                case 'note_frais':
                    if (!empty($validated['expense_type']) && !empty($validated['expense_amount'])) {
                        Expense::create([
                            'user_id'      => $targetUserId,
                            'company_id'   => $validated['company_id'],
                            'type'         => $validated['expense_type'],
                            'amount'       => $validated['expense_amount'],
                            'description'  => $validated['description'] ?? $validated['title'],
                            'receipt_path' => null, // V2 : upload réel
                            'status'       => 'pending',
                            'validated_by' => null,
                        ]);
                    }
                    break;

                case 'document_rh':
                    // Gestion Document RH (avec ou sans fichier)
                    if ($docType) {
                        $path = null;
                        $doc  = null;

                        if ($request->hasFile('doc_file')) {
                            // V2 : upload réel
                            $path = $request->file('doc_file')->store('documents', 'public');

                            // on cherche un doc existant pour ce user + type
                            $doc = Document::where('user_id', $targetUserId)
                                ->where('type', $docType)
                                ->first();

                            if ($doc) {
                                $doc->update([
                                    'file_path'   => $path,
                                    'uploaded_at' => now(),
                                    'expires_at'  => $validated['document_expires_at'] ?? $doc->expires_at,
                                    'status'      => 'pending',
                                ]);
                            } else {
                                $doc = Document::create([
                                    'user_id'     => $targetUserId,
                                    'type'        => $docType,
                                    'file_path'   => $path,
                                    'uploaded_at' => now(),
                                    'expires_at'  => $validated['document_expires_at'] ?? null,
                                    'signed'      => false,
                                    'signed_at'   => null,
                                    'status'      => 'pending',
                                    'metadata'    => [
                                        'source'    => 'ticket',
                                        'ticket_id' => $ticket->id,
                                    ],
                                ]);
                            }
                        } else {
                            // Pas encore de fichier, mais ticket créé → on peut créer ou MAJ le doc en pending/pas de fichier
                            $doc = Document::where('user_id', $targetUserId)
                                ->where('type', $docType)
                                ->first();

                            if (! $doc) {
                                $doc = Document::create([
                                    'user_id'     => $targetUserId,
                                    'type'        => $docType,
                                    'file_path'   => 'pending_upload',
                                    'uploaded_at' => now(),
                                    'expires_at'  => $validated['document_expires_at'] ?? null,
                                    'signed'      => false,
                                    'signed_at'   => null,
                                    'status'      => 'pending',
                                    'metadata'    => [
                                        'source'    => 'ticket',
                                        'ticket_id' => $ticket->id,
                                    ],
                                ]);
                            }
                        }

                        // Détails JSON du ticket (si colonne details existe)
                        if (Schema::hasColumn('tickets', 'details')) {
                            $details = [
                                'doc_type'   => $docType,
                                'expires_at' => $validated['document_expires_at'] ?? null,
                            ];
                            if (!empty($doc)) {
                                $details['document_id'] = $doc->id;
                                $details['file_path']   = $doc->file_path;
                            }

                            $ticket->details = $details;
                            $ticket->save();
                        }
                    }
                    break;

                case 'incident':
                case 'autre':
                default:
                    // V1 : rien de plus, tout est déjà dans le ticket
                    break;
            }

            DB::commit();

            $ticket->load(['creator', 'assignee']);

            if ($ticket->creator) {
                $ticket->creator->full_name = trim(($ticket->creator->first_name ?? '') . ' ' . ($ticket->creator->last_name ?? ''));
            }
            if ($ticket->assignee) {
                $ticket->assignee->full_name = trim(($ticket->assignee->first_name ?? '') . ' ' . ($ticket->assignee->last_name ?? ''));
            }

            return response()->json([
                'success' => true,
                'ticket'  => $ticket,
                'message' => 'Ticket créé',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BacklogController@store] Erreur lors de la création du ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du ticket',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * MAJ du statut du ticket (valide / refuse / en_attente).
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (!in_array($role, ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:en_attente,valide,refuse',
        ]);

        $ticket->status = $data['status'];
        $ticket->save();

        return response()->json([
            'success' => true,
            'ticket'  => $ticket,
        ]);
    }

    /**
     * Détail d’un ticket pour la modale.
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        $ticket->load(['company', 'creator', 'assignee', 'relatedUser']);

        $isAdminLike = in_array($role, ['admin', 'chef_equipe', 'superadmin']);
        $isOwner     = $role === 'employe'
            && ($ticket->created_by === $user->id || $ticket->assigned_to === $user->id);

        if (! $isAdminLike && ! $isOwner) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($ticket->creator) {
            $ticket->creator->full_name = trim(($ticket->creator->first_name ?? '') . ' ' . ($ticket->creator->last_name ?? ''));
        }
        if ($ticket->assignee) {
            $ticket->assignee->full_name = trim(($ticket->assignee->first_name ?? '') . ' ' . ($ticket->assignee->last_name ?? ''));
        }
        if ($ticket->relatedUser) {
            $ticket->relatedUser->full_name = trim(($ticket->relatedUser->first_name ?? '') . ' ' . ($ticket->relatedUser->last_name ?? ''));
        }

        return response()->json([
            'id'          => $ticket->id,
            'title'       => $ticket->title,
            'description' => $ticket->description,
            'type'        => $ticket->type,
            'priority'    => $ticket->priority,
            'status'      => $ticket->status,
            'created_at'  => $ticket->created_at,
            'due_date'    => $ticket->due_date,
            'company'     => [
                'id'   => $ticket->company?->id,
                'name' => $ticket->company?->name,
            ],
            'creator'     => $ticket->creator ? [
                'id'        => $ticket->creator->id,
                'full_name' => $ticket->creator->full_name,
                'email'     => $ticket->creator->email,
            ] : null,
            'assignee'    => $ticket->assignee ? [
                'id'        => $ticket->assignee->id,
                'full_name' => $ticket->assignee->full_name,
                'email'     => $ticket->assignee->email,
            ] : null,
            'related_user'=> $ticket->relatedUser ? [
                'id'        => $ticket->relatedUser->id,
                'full_name' => $ticket->relatedUser->full_name,
                'email'     => $ticket->relatedUser->email,
            ] : null,

            // Champs spécifiques pour la modale détail
            'leave_type'          => $ticket->leave_type,
            'leave_start_date'    => $ticket->leave_start_date,
            'leave_end_date'      => $ticket->leave_end_date,

            'expense_type'        => $ticket->expense_type,
            'expense_amount'      => $ticket->expense_amount,
            'expense_date'        => $ticket->expense_date,

            'document_type'       => $ticket->document_type,
            'document_expires_at' => $ticket->document_expires_at,

            'incident_severity'   => $ticket->incident_severity,

            // Si tu as une colonne JSON "details" castée en array dans le modèle
            'details'             => $ticket->details ?? null,
        ]);
    }

}
