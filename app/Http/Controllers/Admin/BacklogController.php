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
            $isEmployee = $roleName === 'employe';
            $onlyMine   = $request->boolean('mine', false) || $isEmployee;

            if (!in_array($roleName, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }

            // --- paramètres reçus ---
            $companyId  = $isEmployee
                ? $user->company_id
                : $request->query('company_id', $user->company_id);

            $type       = $request->query('type');                // conge, note_frais, ...
            $status     = $request->query('status');              // en_attente, valide, refuse
            $employeeId = $isEmployee ? null : $request->query('employee_id');
            $search     = $request->query('search');              // texte libre

            // 🔹 nouveaux filtres spécifiques
            $leaveStart        = $request->query('leave_start');        // YYYY-MM-DD
            $leaveEnd          = $request->query('leave_end');          // YYYY-MM-DD
            $expenseMin        = $request->query('expense_min');        // montant min
            $expenseMax        = $request->query('expense_max');        // montant max
            $documentType      = $request->query('document_type');      // type doc RH
            $incidentSeverity  = $request->query('incident_severity');  // mineur / majeur / critique

            Log::info('[BacklogController@index] Filtres', [
                'user_id'          => $user->id,
                'role'             => $roleName,
                'company_id'       => $companyId,
                'type'             => $type,
                'status'           => $status,
                'employee_id'      => $employeeId,
                'search'           => $search,
                'onlyMine'         => $onlyMine,
                'leave_start'      => $leaveStart,
                'leave_end'        => $leaveEnd,
                'expense_min'      => $expenseMin,
                'expense_max'      => $expenseMax,
                'document_type'    => $documentType,
                'incident_severity'=> $incidentSeverity,
            ]);

            // --- requête de base ---
            $query = Ticket::with(['creator', 'assignee', 'company', 'relatedUser'])
                ->where('company_id', $companyId)
                ->when($onlyMine, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('created_by', $user->id)
                            ->orWhere('assigned_to', $user->id);
                    });
                })
                ->latest('created_at');

            // type (on ne gère plus "all" ici : si vide => pas de filtre)
            if ($type && $type !== 'all') {
                $query->where('type', $type);
            }

            // statut
            if ($status) {
                $query->where('status', $status);
            }

            // filtrage par employé (créateur ou employé concerné)
            if ($employeeId) {
                $query->where(function ($q) use ($employeeId) {
                    $q->where('created_by', $employeeId)
                    ->orWhere('related_user_id', $employeeId);
                });
            }

            // 🔹 Filtres spécifiques par type

            // -- Congés : bornes sur les dates de congés --
            if ($leaveStart || $leaveEnd) {
                // si on filtre par congés, on force type = 'conge' (sauf si déjà filtré)
                if (!$type) {
                    $query->where('type', 'conge');
                }

                if ($leaveStart) {
                    $query->whereDate('leave_start_date', '>=', $leaveStart);
                }
                if ($leaveEnd) {
                    $query->whereDate('leave_end_date', '<=', $leaveEnd);
                }
            }

            // -- Notes de frais : bornes sur le montant --
            if ($expenseMin !== null && $expenseMin !== '') {
                if (!$type) {
                    $query->where('type', 'note_frais');
                }
                $query->where('expense_amount', '>=', (float) $expenseMin);
            }
            if ($expenseMax !== null && $expenseMax !== '') {
                if (!$type) {
                    $query->where('type', 'note_frais');
                }
                $query->where('expense_amount', '<=', (float) $expenseMax);
            }

            // -- Documents RH : type de document --
            if ($documentType) {
                if (!$type) {
                    $query->where('type', 'document_rh');
                }
                $query->where('document_type', $documentType);
            }

            // -- Incidents : gravité --
            if ($incidentSeverity) {
                if (!$type) {
                    $query->where('type', 'incident');
                }
                $query->where('incident_severity', $incidentSeverity);
            }

            // recherche texte : titre, description, nom/prénom créateur OU employé concerné
            if ($search) {
                $s = '%' . trim($search) . '%';
                $query->where(function ($q) use ($s) {
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

            $tickets = $query->get();

            // Ajout d'un full_name sur les relations pour simplifier le JS
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

            // Stats : si employé / mine => stats sur la même base que la liste
            $baseStats = $onlyMine
                ? clone $query
                : Ticket::where('company_id', $companyId);

            $stats = [
                'total'     => (clone $baseStats)->count(),
                'pending'   => (clone $baseStats)->where('status', 'en_attente')->count(),
                'validated' => (clone $baseStats)->where('status', 'valide')->count(),
                'refused'   => (clone $baseStats)->where('status', 'refuse')->count(),
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
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (!in_array($role, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Validation (on supporte à la fois "document_type" et un éventuel "doc_type")
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:conge,note_frais,incident,document_rh,autre',
            'description'     => 'nullable|string',
            'assignee_id'     => 'nullable|exists:users,id',
            'priority'        => 'nullable|in:basse,moyenne,haute',
            'due_date'        => 'nullable|date',
            'related_user_id' => 'nullable|exists:users,id',
            'company_id'      => 'required|exists:companies,id',

            // CONGÉ
            'leave_type'       => 'nullable|in:CP,SansSolde,Exceptionnel,Maladie',
            'leave_start_date' => 'nullable|date',
            'leave_end_date'   => 'nullable|date|after_or_equal:leave_start_date',

            // NOTE DE FRAIS
            'expense_type'   => 'nullable|in:repas,peage,hebergement,km',
            'expense_amount' => 'nullable|numeric|min:0',
            'expense_date'   => 'nullable|date',

            // DOCUMENT RH (V1, sans upload obligatoire)
            'document_type'        => 'nullable|string|max:255',
            'document_expires_at'  => 'nullable|date',

            // compat éventuelle avec ancien nommage
            'doc_type'        => 'nullable|string|max:255',
            'doc_file'        => 'nullable|file|max:5120',

            // INCIDENT
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
