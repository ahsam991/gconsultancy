<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithRole(string $role): User
    {
        $r = Role::create(['name' => $role, 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->role_id = $r->id;
        $user->save();

        return $user->fresh();
    }

    public function test_staff_cannot_access_admin_only_users(): void
    {
        $staff = $this->userWithRole('staff');

        $this->actingAs($staff)->get('/crm/users')->assertForbidden();
    }

    public function test_candidate_cannot_access_crm_candidates(): void
    {
        $candidate = $this->userWithRole('candidate');

        $this->actingAs($candidate)->get('/crm/candidates')->assertForbidden();
    }

    public function test_candidate_cannot_access_crm_leads(): void
    {
        $candidate = $this->userWithRole('candidate');

        $this->actingAs($candidate)->get('/crm/leads')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/crm/candidates')->assertRedirect();
        $this->get('/portal/dashboard')->assertRedirect();
    }

    public function test_permission_gate_allows_and_denies(): void
    {
        $role = Role::create(['name' => 'staff', 'guard_name' => 'web']);
        $perm = Permission::create(['name' => 'candidates.view', 'guard_name' => 'web']);
        $role->permissions()->attach($perm->id);

        $user = User::factory()->create();
        $user->role_id = $role->id;
        $user->save();

        $this->assertTrue($user->fresh()->can('candidates.view'));
        $this->assertFalse($user->fresh()->can('candidates.delete'));
    }

    public function test_admin_has_all_permissions(): void
    {
        $admin = $this->userWithRole('admin');

        $this->assertTrue($admin->can('anything.at.all'));
    }
}
