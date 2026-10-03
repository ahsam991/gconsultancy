<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    protected function ensureAdmin(): void
    {
        $role = auth()->user()?->role?->name;
        if ($role !== 'admin') {
            abort(403, 'Admin access only.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        $branches = Branch::with('manager')->orderBy('name')->paginate(15);

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $managers = User::whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'manager']))
            ->orderBy('name')->get();
        $branch = new Branch(['active' => true]);

        return view('branches.create', compact('managers', 'branch'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        $branch = Branch::create($data);
        AuditService::log('branch.created', $branch, null, $branch->toArray());

        return redirect()->route('branches.show', $branch)->with('status', 'Branch created.');
    }

    public function show(Branch $branch)
    {
        $this->ensureAdmin();
        $branch->load(['manager', 'users', 'candidates']);

        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $this->ensureAdmin();
        $managers = User::whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'manager']))
            ->orderBy('name')->get();

        return view('branches.edit', compact('branch', 'managers'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,'.$branch->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $old = $branch->toArray();
        $branch->update($data);
        AuditService::log('branch.updated', $branch, $old, $branch->toArray());

        return redirect()->route('branches.show', $branch)->with('status', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        $this->ensureAdmin();
        $branch->delete();
        AuditService::log('branch.deleted', null, null, ['id' => $branch->id]);

        return redirect()->route('branches.index')->with('status', 'Branch deleted.');
    }
}
