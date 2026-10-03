<?php

namespace App\Policies;

use App\Models\CandidateDocument;
use App\Models\User;

class DocumentPolicy
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
        return in_array($user->role?->name, ['admin', 'manager', 'staff', 'candidate'], true);
    }

    protected function scope(User $user, CandidateDocument $doc): bool
    {
        $role = $user->role?->name;
        $candidate = $doc->candidate;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            if ($candidate && (int) $candidate->assigned_staff_id === (int) $user->id) {
                return true;
            }
            if ($doc->application && (int) $doc->application->assigned_staff_id === (int) $user->id) {
                return true;
            }
            return false;
        }
        if ($role === 'candidate') {
            return $candidate && (int) $candidate->user_id === (int) $user->id;
        }
        return false;
    }

    public function view(User $user, CandidateDocument $doc): bool
    {
        return $this->scope($user, $doc);
    }

    public function download(User $user, CandidateDocument $doc): bool
    {
        return $this->scope($user, $doc);
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'manager', 'staff', 'candidate'], true);
    }

    public function verify(User $user, CandidateDocument $doc): bool
    {
        return in_array($user->role?->name, ['admin', 'manager'], true);
    }

    public function delete(User $user, CandidateDocument $doc): bool
    {
        return in_array($user->role?->name, ['admin', 'manager'], true);
    }
}
