<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        Log::info('🔹 [UserController@index] Début de la requête /admin/users');

        try {
            // Récupère les utilisateurs avec rôle et société
            $users = User::with(['role:id,name', 'company:id,name'])
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'created_at']);

            // Récupère la dernière activité depuis la table sessions
            $lastSessions = DB::table('sessions')
                ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->pluck('last_activity', 'user_id');

            // Mappe la valeur UNIX timestamp vers datetime
            foreach ($users as $user) {
                if ($lastSessions->has($user->id)) {
                    $user->last_login_at = now()->setTimestamp($lastSessions[$user->id]);
                } else {
                    $user->last_login_at = null;
                }
            }

            Log::info('✅ [UserController@index] Succès', ['count' => $users->count()]);
            return response()->json($users, 200);

        } catch (\Throwable $e) {
            Log::error('❌ [UserController@index] Erreur : ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Erreur serveur : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('🔹 [UserController@store] Données reçues', $request->all());

        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name'  => ['required', 'string', 'max:100'],
                'email'      => ['required', 'email', 'unique:users,email'],
                'password'   => ['required', 'string', 'min:8'],
                'role_id'    => ['required', 'exists:roles,id'],
                'company_id' => ['nullable', 'exists:companies,id'],
            ]);

            $user = User::create([
                'id'         => Str::uuid(),
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'role_id'    => $validated['role_id'],
                'company_id' => $validated['company_id'],
                'status'     => 'active',
            ]);

            Log::info('✅ [UserController@store] Utilisateur créé', ['user_id' => $user->id]);
            return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user]);
        } catch (\Throwable $e) {
            Log::error('❌ [UserController@store] Erreur : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Erreur serveur : ' . $e->getMessage(),
            ], 500);
        }
    }
}
