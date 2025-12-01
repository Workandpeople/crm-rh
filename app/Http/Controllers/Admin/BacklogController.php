<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BacklogController extends Controller
{
    /**
     * Liste filtrée des tickets (JSON).
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $roleName = $user->role->name ?? null;
            $isEmployee = $roleName === 'employe';
            $onlyMine = $request->boolean('mine', false) || $isEmployee;

            if (!in_array($roleName, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
                abort(403, 'Accès non autorisé');
            }

            // --- paramètres reçus ---
            $companyId   = $isEmployee
                ? $user->company_id
                : $request->query('company_id', $user->company_id);
            $type        = $request->query('type');         // conge, note_frais…
            $status      = $request->query('status');       // en_attente, valide, refuse
            $employeeId  = $isEmployee ? null : $request->query('employee_id');  // créateur
            $start       = $request->query('start');        // date YYYY-MM-DD
            $end         = $request->query('end');          // date YYYY-MM-DD
            $search      = $request->query('search');       // texte libre

            Log::info('[BacklogController@index] Filtres', [
                'company_id'  => $companyId,
                'type'        => $type,
                'status'      => $status,
                'employee_id' => $employeeId,
                'start'       => $start,
                'end'         => $end,
                'search'      => $search,
            ]);

            // --- requête de base ---
            $query = Ticket::with(['creator', 'assignee', 'company'])
                ->where('company_id', $companyId)
                ->when($onlyMine, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('created_by', $user->id)
                            ->orWhere('assigned_to', $user->id);
                    });
                })
                ->latest('created_at');

            // type
            if ($type) {
                $query->where('type', $type);
            }

            // statut
            if ($status) {
                $query->where('status', $status);
            }

            // créateur
            if ($employeeId) {
                $query->where('created_by', $employeeId);
            }

            // période sur created_at
            if ($start) {
                $query->whereDate('created_at', '>=', $start);
            }
            if ($end) {
                $query->whereDate('created_at', '<=', $end);
            }

            // recherche texte : titre, description, nom/prénom créateur
            if ($search) {
                $s = '%' . trim($search) . '%';
                $query->where(function ($q) use ($s) {
                    $q->where('title', 'LIKE', $s)
                      ->orWhere('description', 'LIKE', $s)
                      ->orWhereHas('creator', function ($q2) use ($s) {
                          $q2->where('first_name', 'LIKE', $s)
                             ->orWhere('last_name', 'LIKE', $s)
                             ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$s]);
                      });
                });
            }

            $tickets = $query->get();

            // stats globales de la société (on peut les laisser sans filtres détaillés)
            $baseStats = ($isEmployee || $onlyMine)
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
     * Options : utilisateurs pouvant recevoir un ticket.
     */
    public function options(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            abort(403, 'Accès non autorisé');
        }

        $companyId = $request->query('company_id', $user->company_id);

        $assignees = User::where('company_id', $companyId)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $assignees->each(
            fn($u) => $u->full_name = trim($u->first_name . ' ' . $u->last_name)
        );

        return response()->json([
            'assignees' => $assignees,
        ]);
    }

    /**
     * Création d'un ticket.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (! in_array($role, ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:conge,note_frais,incident,autre,document_rh',
            'description'     => 'nullable|string',
            'assignee_id'     => 'nullable|exists:users,id',
            'priority'        => 'nullable|in:basse,moyenne,haute',
            'due_date'        => 'nullable|date',
            'related_user_id' => 'nullable|exists:users,id',
            'company_id'      => 'required|exists:companies,id',
            'doc_type'        => 'nullable|string|max:100',
            'doc_file'        => 'nullable|file|max:5120',
        ]);

        // doc_type obligatoire si type document_rh
        if (($validated['type'] ?? null) === 'document_rh' && empty($validated['doc_type'])) {
            return response()->json([
                'message' => 'Type de document requis',
            ], 422);
        }

        // Un employé ne peut créer que dans sa société
        if ($role === 'employe') {
            $validated['company_id'] = $user->company_id;
        }

        // Pour un document refusé, on supprime le ticket précédent avant d'en recréer un
        if (($validated['type'] ?? null) === 'document_rh' && !empty($validated['doc_type'])) {
            Ticket::where('type', 'document_rh')
                ->where('created_by', $user->id)
                ->where('status', 'refuse')
                ->where('details->doc_type', $validated['doc_type'])
                ->delete();
        }

        $ticket = Ticket::create([
            'company_id'      => $validated['company_id'],
            'created_by'      => $user->id,
            'assigned_to'     => $validated['assignee_id'] ?? null,
            'type'            => $validated['type'],
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? '',
            'priority'        => $validated['priority'] ?? 'moyenne',
            'status'          => 'en_attente',
            'due_date'        => $validated['due_date'] ?? null,
            'related_user_id' => $validated['related_user_id'] ?? null,
        ]);

        // Si dépôt document_rh avec fichier → création/MAJ Document et détails ticket
        if (($validated['type'] ?? null) === 'document_rh') {
            $path = null;
            if ($request->hasFile('doc_file')) {
                $path = $request->file('doc_file')->store('documents', 'public');
                $doc = Document::where('user_id', $user->id)
                    ->where('type', $validated['doc_type'])
                    ->first();
                if ($doc) {
                    $doc->update([
                        'file_path'   => $path,
                        'uploaded_at' => now(),
                        'status'      => 'pending',
                    ]);
                } else {
                    $doc = Document::create([
                        'user_id'    => $user->id,
                        'type'       => $validated['doc_type'],
                        'file_path'  => $path,
                        'uploaded_at'=> now(),
                        'status'     => 'pending',
                    ]);
                }
                $ticket->details = [
                    'doc_type' => $validated['doc_type'],
                    'file_path'=> $path,
                    'document_id' => $doc->id,
                ];
                $ticket->status = 'en_attente';
                $ticket->save();
            } else {
                $ticket->details = [
                    'doc_type' => $validated['doc_type'],
                ];
                $ticket->save();
            }
        }

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
    }

    /**
     * Mise à jour du statut d’un ticket.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $role = $user->role->name ?? null;

        if (! in_array($role, ['admin', 'chef_equipe', 'superadmin'])) {
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

        if (! in_array($role, ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $ticket->load(['company', 'creator', 'assignee', 'relatedUser']);

        $authorizedEmployee = $role === 'employe'
            && ($ticket->created_by === $user->id || $ticket->assigned_to === $user->id);

        if (!in_array($role, ['admin', 'chef_equipe', 'superadmin']) && ! $authorizedEmployee) {
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
        ]);
    }
}
