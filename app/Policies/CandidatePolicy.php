<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (($user->role?->name) === 'admin') {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'manager', 'staff'], true);
    }

    public function view(User $user, Candidate $candidate): bool
    {
        $role = $user->role?->name;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            return (int) $candidate->assigned_staff_id === (int) $user->id;
        }
        if ($role === 'candidate') {
            return (int) $candidate->user_id === (int) $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'manager'], true);
    }

    public function update(User $user, Candidate $candidate): bool
    {
        $role = $user->role?->name;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            return (int) $candidate->assigned_staff_id === (int) $user->id;
        }
        return false;
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        $role = $user->role?->name;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            return (int) $candidate->assigned_staff_id === (int) $user->id;
        }
        return false;
    }
}
