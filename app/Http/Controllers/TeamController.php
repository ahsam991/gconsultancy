<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('manager')->withCount('members')->orderBy('name')->paginate(15);
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $managers = User::whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'manager']))->orderBy('name')->get();
        return view('teams.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);
        $team = Team::create($data + ['active' => true]);
        AuditService::log('team.created', null, null, $team->toArray());
        return redirect()->route('teams.show', $team)->with('status', 'Team created.');
    }

    public function show(Team $team)
    {
        $team->load(['manager', 'members.role']);
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $managers = User::whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'manager']))->orderBy('name')->get();
        return view('teams.edit', compact('team', 'managers'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,'.$team->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'active' => 'nullable|boolean',
        ]);
        $team->update($data);
        AuditService::log('team.updated', null, null, $team->toArray());
        return redirect()->route('teams.show', $team)->with('status', 'Team updated.');
    }

    public function destroy(Team $team)
    {
        User::where('team_id', $team->id)->update(['team_id' => null]);
        $team->delete();
        AuditService::log('team.deleted', null, null, ['id' => $team->id]);
        return redirect()->route('teams.index')->with('status', 'Team deleted.');
    }
}
