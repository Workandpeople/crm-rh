<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Team, Company, User, Role};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with([
            'company:id,name',
            'leader:id,first_name,last_name'
        ])->orderBy('name')->get([
            'id','company_id','leader_user_id','name','description','created_at'
        ]);

        return response()->json($teams);
    }

    public function options()
    {
        return response()->json([
            'companies' => Company::orderBy('name')->get(['id','name']),
            'leaders' => User::whereHas('role', fn($r) => $r->where('name','chef_equipe'))
                ->orderBy('last_name')->get(['id','first_name','last_name']),
        ]);
    }

    public function show(Team $team)
    {
        return response()->json(
            $team->load(['company:id,name','leader:id,first_name,last_name'])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required','string','max:190'],
            'company_id'     => ['required','uuid', Rule::exists('companies','id')],
            'leader_user_id' => ['nullable','uuid', Rule::exists('users','id')],
            'description'    => ['nullable','string','max:500'],
        ]);

        $team = Team::create($validated);

        return response()->json(['message'=>'Équipe créée','team'=>$team],201);
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name'           => ['required','string','max:190'],
            'company_id'     => ['required','uuid', Rule::exists('companies','id')],
            'leader_user_id' => ['nullable','uuid', Rule::exists('users','id')],
            'description'    => ['nullable','string','max:500'],
        ]);

        $team->update($validated);

        return response()->json(['message'=>'Équipe mise à jour','team'=>$team]);
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return response()->json(['message'=>'Équipe supprimée']);
    }
}
