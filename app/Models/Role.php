<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'guard_name'];

    /**
     * Get the permissions assigned to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission($permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }
}
