<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Liste des documents RH (JSON pour l'admin)
     * Filtres : company_id, team_id, employee_id, type, status, search
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Qui a le droit ?
        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Filtre société / équipe (contexte admin)
        $companyId   = $request->query('company_id', $user->company_id);
        $teamId      = $request->query('team_id');
        $employeeId  = $request->query('employee_id');
        $type        = $request->query('type');   // CNI / Contrat / ...
        $status      = $request->query('status'); // pending / valid / rejected / expired
        $search      = trim((string) $request->query('search', ''));

        // Base : documents de la société (via user.company_id)
        $baseQuery = Document::with(['user'])
            ->whereHas('user', function ($q) use ($companyId, $teamId) {
                if ($companyId) {
                    $q->where('company_id', $companyId);
                }
                if ($teamId) {
                    $q->where('team_id', $teamId);
                }
            });

        // Stats basées sur la base (sans les filtres fins)
        $statsQuery = clone $baseQuery;

        $stats = [
            'total'           => (clone $statsQuery)->count(),
            'validated'       => (clone $statsQuery)->where('status', 'valid')->count(),
            'pending'         => (clone $statsQuery)->where('status', 'pending')->count(),
            'refused_expired' => (clone $statsQuery)->whereIn('status', ['rejected', 'expired'])->count(),
        ];

        // Filtres détaillés (employé, type, statut, recherche)
        $query = clone $baseQuery;

        if (!empty($employeeId)) {
            $query->where('user_id', $employeeId);
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $documents = $query
            ->orderByDesc('uploaded_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'documents' => $documents,
            'stats'     => $stats,
        ]);
    }

    /**
     * Mise à jour du statut : pending / valid / rejected / expired
     */
    public function updateStatus(Request $request, Document $document)
    {
        $user = Auth::user();

        if (!in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin'])) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        // Sécurité : l’admin ne touche que à sa société
        if ($user->company_id && $document->user && $document->user->company_id !== $user->company_id) {
            return response()->json(['message' => 'Document hors de votre société'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,valid,rejected,expired',
        ]);

        $document->status = $data['status'];
        $document->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Statut mis à jour',
            'document' => $document->load('user'),
        ]);
    }

    /**
     * Suppression d’un document RH
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();

        $isManager = in_array($user->role->name ?? '', ['admin', 'chef_equipe', 'superadmin']);
        $isOwner = $document->user_id === $user->id;

        if (! $isManager && ! $isOwner) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        if ($isManager && $user->company_id && $document->user && $document->user->company_id !== $user->company_id) {
            return response()->json(['message' => 'Document hors de votre société'], 403);
        }

        // Optionnel : suppression du fichier
        if ($document->file_path && Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        // Supprime un ticket document_rh associé (créé par l’utilisateur et même doc_type)
        if ($isOwner) {
            $ticket = Ticket::where('type', 'document_rh')
                ->where('created_by', $document->user_id)
                ->where('details->doc_type', $document->type)
                ->orderByDesc('created_at')
                ->first();
            if ($ticket) {
                $ticket->delete();
            }
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document supprimé',
        ]);
    }
}
