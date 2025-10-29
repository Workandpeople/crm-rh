<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\{User, Role, Company};
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['role:id,name', 'company:id,name'])
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'created_at']);

            $lastSessions = DB::table('sessions')
                ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->pluck('last_activity', 'user_id');

            foreach ($users as $user) {
                $user->last_login_at = $lastSessions->has($user->id)
                    ? now()->setTimestamp($lastSessions[$user->id])
                    : null;
            }

            return response()->json($users, 200);
        } catch (\Throwable $e) {
            Log::error('[UserController@index]', ['error' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function options()
    {
        return response()->json([
            'roles' => Role::select('id', 'name', 'label')->orderBy('name')->get(),
            'companies' => Company::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:100'],
                'last_name'  => ['required', 'string', 'max:100'],
                'email'      => ['required', 'email', 'unique:users,email'],
                'role_id'    => ['required', 'exists:roles,id'],
                'company_id' => ['nullable', 'exists:companies,id'],
            ]);

            $user = User::create([
                'id'         => Str::uuid(),
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'password'   => Hash::make('Wap92!'),
                'role_id'    => $validated['role_id'],
                'company_id' => $validated['company_id'],
                'status'     => 'active',
            ]);

            return response()->json(['message' => 'Utilisateur créé', 'user' => $user]);
        } catch (\Throwable $e) {
            Log::error('[UserController@store]', ['error' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json($user->load(['role:id,name', 'company:id,name']));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email,' . $user->id],
            'role_id'    => ['required', 'exists:roles,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'status'     => ['nullable', 'in:active,inactive,pending'],
        ]);

        $user->update($validated);
        return response()->json(['message' => 'Utilisateur mis à jour', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    public function resetPassword(User $user)
    {
        try {
            Password::sendResetLink(['email' => $user->email]);
            return response()->json(['message' => 'Lien de réinitialisation envoyé à ' . $user->email]);
        } catch (\Throwable $e) {
            Log::error('[UserController@resetPassword]', ['error' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => 'Erreur lors de l’envoi du lien.'], 500);
        }
    }
}
