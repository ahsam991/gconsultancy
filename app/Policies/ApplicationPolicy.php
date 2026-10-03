<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
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

    public function view(User $user, Application $application): bool
    {
        $role = $user->role?->name;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            if ((int) $application->assigned_staff_id === (int) $user->id) {
                return true;
            }
            return $application->candidate
                && (int) $application->candidate->assigned_staff_id === (int) $user->id;
        }
        if ($role === 'candidate') {
            return $application->candidate
                && (int) $application->candidate->user_id === (int) $user->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'manager', 'staff'], true);
    }

    public function update(User $user, Application $application): bool
    {
        $role = $user->role?->name;
        if ($role === 'manager') {
            return true;
        }
        if ($role === 'staff') {
            if ((int) $application->assigned_staff_id === (int) $user->id) {
                return true;
            }
            return $application->candidate
                && (int) $application->candidate->assigned_staff_id === (int) $user->id;
        }
        if ($role === 'candidate') {
            return $application->candidate
                && (int) $application->candidate->user_id === (int) $user->id;
        }
        return false;
    }

    public function delete(User $user, Application $application): bool
    {
        return in_array($user->role?->name, ['admin', 'manager'], true);
    }

    public function changeStatus(User $user, Application $application): bool
    {
        return $this->update($user, $application);
    }
}
