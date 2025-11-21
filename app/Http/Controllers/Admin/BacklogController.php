<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class BacklogController extends Controller
{
    /**
     * Retourne la liste filtrée des tickets (JSON).
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            Log::info('[BacklogController@index] Début chargement', [
                'user_id' => $user->id ?? null,
                'role'    => $user->role->name ?? 'inconnu',
            ]);

            if (!in_array($user->role->name, ['admin', 'chef_equipe', 'superadmin'])) {
                Log::warning('[BacklogController@index] Accès refusé pour le rôle : ' . $user->role->name);
                abort(403, 'Accès non autorisé');
            }

            $companyId = $request->query('company_id', $user->company_id);
            $teamId    = $request->query('team_id');
            $type      = $request->query('type', 'all');

            Log::info('[BacklogController@index] Paramètres reçus', [
                'company_id' => $companyId,
                'team_id'    => $teamId,
                'type'       => $type,
            ]);

            // Vérif existence de tickets pour cette entreprise
            $exists = Ticket::where('company_id', $companyId)->exists();
            Log::info('[BacklogController@index] Tickets existants pour company ?', ['exists' => $exists]);

            $query = Ticket::with(['creator', 'assignee'])
                ->where('company_id', $companyId)
                ->latest();

            if ($teamId) {
                Log::info('[BacklogController@index] Filtrage par équipe', ['team_id' => $teamId]);
                $query->whereHas('creator', fn($q) => $q->where('team_id', $teamId));
            }

            if ($type !== 'all') {
                Log::info('[BacklogController@index] Filtrage par type', ['type' => $type]);
                $query->where('type', $type);
            }

            $tickets = $query->get();
            Log::info('[BacklogController@index] Tickets récupérés', ['count' => $tickets->count()]);

            // Détail rapide du premier ticket pour vérif
            if ($tickets->isNotEmpty()) {
                Log::debug('[BacklogController@index] Exemple ticket', [
                    'id'    => $tickets->first()->id,
                    'title' => $tickets->first()->title,
                    'type'  => $tickets->first()->type,
                    'status'=> $tickets->first()->status,
                ]);
            }

            $stats = [
                'total'     => Ticket::where('company_id', $companyId)->count(),
                'pending'   => Ticket::where('company_id', $companyId)->where('status', 'en_attente')->count(),
                'validated' => Ticket::where('company_id', $companyId)->where('status', 'valide')->count(),
                'refused'   => Ticket::where('company_id', $companyId)->where('status', 'refuse')->count(),
            ];

            Log::info('[BacklogController@index] Statistiques calculées', $stats);

            return response()->json([
                'tickets' => $tickets,
                'stats'   => $stats,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('[BacklogController@index] Erreur', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
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

        // Pour que t.creator.full_name fonctionne dans le JS :
        $assignees->each(fn ($u) => $u->full_name = trim($u->first_name . ' ' . $u->last_name));

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

        // Qui a le droit de créer un ticket ?
        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin', 'employe'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Validation des champs
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:conge,note_frais,incident,autre',
            'description'     => 'nullable|string',
            'assignee_id'     => 'nullable|exists:users,id',
            'priority'        => 'nullable|in:basse,moyenne,haute',
            'due_date'        => 'nullable|date',
            'related_user_id' => 'nullable|exists:users,id',
            'company_id'      => 'required|exists:companies,id',
        ]);

        // Création du ticket
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

        // On renvoie le ticket avec les relations si tu veux les exploiter côté JS plus tard
        return response()->json([
            'success' => true,
            'ticket'  => $ticket->load(['creator', 'assignee']),
        ], 201);
    }


    /**
     * Mise à jour du statut (valide / refuse / en_attente).
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            abort(403, 'Accès non autorisé');
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

    public function show(Ticket $ticket)
{
    $user = Auth::user();

    if (!in_array($user->role->name ?? '', ['admin','chef_equipe','superadmin'])) {
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }

    $ticket->load(['company', 'creator', 'assignee', 'relatedUser']);

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
        'creator'     => [
            'id'        => $ticket->creator?->id,
            'full_name' => $ticket->creator?->full_name,
            'email'     => $ticket->creator?->email,
        ],
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
