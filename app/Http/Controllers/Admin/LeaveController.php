<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Liste des congés (JSON) + stats pour l’Admin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Rôles autorisés
        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            abort(403, 'Accès non autorisé');
        }

        // Contexte
        $companyId = $request->query('company_id', $user->company_id);
        $teamId    = $request->query('team_id');
        $type      = $request->query('type');   // CP, SansSolde, Exceptionnel, Maladie, ou null
        $status    = $request->query('status'); // pending, approved, rejected, ou null
        $search    = $request->query('q');      // recherche sur le nom/prénom

        // Requête de base : congés des utilisateurs de la société (et éventuellement équipe)
        $query = Leave::with(['user', 'validator'])
            ->whereHas('user', function ($q) use ($companyId, $teamId) {
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                if ($teamId) {
                    $q->where('team_id', $teamId);
                }
            });

        // Filtre type
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // Filtre statut
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Recherche sur nom/prénom employé
        if ($search) {
            $s = '%' . trim($search) . '%';
            $query->whereHas('user', function ($q) use ($s) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$s])
                  ->orWhere('first_name', 'like', $s)
                  ->orWhere('last_name', 'like', $s);
            });
        }

        $leaves = $query->orderByDesc('start_date')->get();

        // Stats globales pour la société
        $baseStats = Leave::whereHas('user', function ($q) use ($companyId) {
            if ($companyId) {
                $q->where('company_id', $companyId);
            }
        });

        $stats = [
            'total'    => (clone $baseStats)->count(),
            'pending'  => (clone $baseStats)->where('status', 'pending')->count(),
            'approved' => (clone $baseStats)->where('status', 'approved')->count(),
            'rejected' => (clone $baseStats)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'leaves' => $leaves,
            'stats'  => $stats,
        ]);
    }

    /**
     * Détail d’un congé (pour la modale détail).
     */
    public function show(Leave $leave)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            abort(403, 'Accès non autorisé');
        }

        // Sécurité : on vérifie que le congé appartient bien à la même société
        if ($user->company_id && $leave->user && $leave->user->company_id !== $user->company_id) {
            abort(403, 'Accès non autorisé à ce congé');
        }

        // On charge les relations pour la modale
        $leave->load(['user', 'validator']);

        return response()->json($leave);
    }

    /**
     * Changer le statut d’un congé (pending / approved / rejected)
     * + enregistre le validateur + commentaire éventuel.
     */
    public function updateStatus(Request $request, Leave $leave)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Vérif société
        if ($user->company_id && $leave->user && $leave->user->company_id !== $user->company_id) {
            return response()->json(['message' => 'Ce congé ne vous appartient pas'], 403);
        }

        $data = $request->validate([
            'status'   => 'required|in:pending,approved,rejected',
            'comments' => 'nullable|string',
        ]);

        $leave->status       = $data['status'];
        $leave->validated_by = in_array($data['status'], ['approved', 'rejected'])
            ? $user->id
            : null;

        if (array_key_exists('comments', $data)) {
            $leave->comments = $data['comments'];
        }

        $leave->save();

        return response()->json([
            'success' => true,
            'leave'   => $leave->load(['user', 'validator']),
        ]);
    }
}
