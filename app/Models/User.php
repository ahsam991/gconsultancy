<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'phone', 'address', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'team_id', 'phone', 'address', 'status'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role assigned to the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function assignedCandidates()
    {
        return $this->hasMany(Candidate::class, 'assigned_staff_id');
    }

    public function assignedApplications()
    {
        return $this->hasMany(Application::class, 'assigned_staff_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function availabilities()
    {
        return $this->hasMany(StaffAvailability::class);
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string|array $permission
     * @param array $arguments
     */
    public function can($permission, $arguments = [])
    {
        // Admin has all permissions
        if ($this->role && $this->role->name === 'admin') {
            return true;
        }

        // Check user's permissions
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if (!$this->can($perm)) {
                    return false;
                }
            }
            return true;
        }

        // Check specific permission
        if ($this->role) {
            return $this->role->permissions->contains('name', $permission);
        }

        return false;
    }

    /**
     * Check if user cannot have a specific permission.
     */
    public function cannot($permission, $arguments = [])
    {
        return !$this->can($permission, $arguments);
    }
}
