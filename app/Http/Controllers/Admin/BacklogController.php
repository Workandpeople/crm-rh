<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
}
