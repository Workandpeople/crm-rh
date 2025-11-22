<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Liste des notes de frais (JSON).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $companyId = $request->query('company_id', $user->company_id);
        $teamId    = $request->query('team_id');

        // On récupère toutes les notes de frais de la société
        $query = Expense::with(['user', 'company'])
            ->where('company_id', $companyId)
            ->latest();

        // Si on veut filtrer par équipe : on passe par le user
        if ($teamId) {
            $query->whereHas('user', function ($q) use ($teamId) {
                $q->where('team_id', $teamId);
            });
        }

        $expenses = $query->get();

        // Stats basées sur ce jeu de données
        $stats = [
            'total'    => $expenses->count(),
            'pending'  => $expenses->where('status', 'pending')->count(),
            'approved' => $expenses->where('status', 'approved')->count(),
            'rejected' => $expenses->where('status', 'rejected')->count(),
            'paid'     => $expenses->where('status', 'paid')->count(),
        ];

        return response()->json([
            'expenses' => $expenses,
            'stats'    => $stats,
        ]);
    }

    /**
     * Détail d'une note de frais (si tu veux une modale détail plus tard).
     */
    public function show(Expense $expense)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $expense->load(['user', 'company', 'validator']);

        return response()->json($expense);
    }

    /**
     * Mise à jour du statut (pending -> approved / rejected / paid).
     */
    public function updateStatus(Request $request, Expense $expense)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,paid',
        ]);

        $expense->status       = $data['status'];
        $expense->validated_by = $user->id;
        $expense->save();

        return response()->json([
            'success' => true,
            'expense' => $expense->fresh(['user', 'company', 'validator']),
        ]);
    }
}
