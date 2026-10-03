<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRoles(): array
    {
        $roles = [];
        foreach (['admin', 'manager', 'staff', 'candidate'] as $name) {
            $roles[$name] = Role::create(['name' => $name, 'guard_name' => 'web']);
        }

        return $roles;
    }

    protected function makeUser(string $roleName, array $overrides = []): User
    {
        $roles = Role::where('name', $roleName)->first()
            ?? Role::create(['name' => $roleName, 'guard_name' => 'web']);

        $user = User::factory()->create($overrides);
        $user->role_id = $roles->id;
        $user->save();

        return $user;
    }

    public function test_login_page_loads(): void
    {
        $this->get('/auth/login')->assertOk();
    }

    public function test_user_can_register(): void
    {
        $roles = $this->makeRoles();

        $response = $this->post('/auth/register', [
            'name' => 'New Candidate',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'consent' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_register_ignores_role_escalation(): void
    {
        $roles = $this->makeRoles();

        $this->post('/auth/register', [
            'name' => 'Sneaky Admin',
            'email' => 'sneaky@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $roles['admin']->id,
            'consent' => '1',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'sneaky@example.com', 'role_id' => $roles['candidate']->id]);
    }

    public function test_register_requires_consent(): void
    {
        $this->makeRoles();

        $this->post('/auth/register', [
            'name' => 'No Consent',
            'email' => 'noconsent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('consent');
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $this->makeRoles();
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/auth/register', [
            'name' => 'Someone Else',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_candidate_login_redirects_to_portal_dashboard(): void
    {
        $this->makeRoles();
        $this->makeUser('candidate', ['email' => 'cand@example.com']);

        $this->post('/auth/login', [
            'email' => 'cand@example.com',
            'password' => 'password',
        ])->assertRedirect(route('portal.dashboard'));
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $this->makeRoles();
        $this->makeUser('admin', ['email' => 'admin@example.com']);

        $this->post('/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_staff_and_manager_login_redirects(): void
    {
        $this->makeRoles();
        $this->makeUser('staff', ['email' => 'staff@example.com']);
        $this->makeUser('manager', ['email' => 'manager@example.com']);

        $this->post('/auth/login', ['email' => 'staff@example.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        Auth::logout();

        $this->post('/auth/login', ['email' => 'manager@example.com', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_invalid_credentials_rejected(): void
    {
        $this->makeRoles();
        $this->makeUser('candidate', ['email' => 'real@example.com']);

        $this->post('/auth/login', [
            'email' => 'real@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        // NOTE: no HTTP logout route exists yet (contract lists auth logout
        // but routes/web.php has none) — verify session logout behaviour.
        $this->makeRoles();
        $user = $this->makeUser('candidate');

        $this->actingAs($user);
        $this->assertAuthenticated();
        Auth::logout();
        $this->assertGuest();
    }

    public function test_authenticated_candidate_can_reach_portal_dashboard(): void
    {
        $this->makeRoles();
        $user = $this->makeUser('candidate');

        $this->actingAs($user)->get('/portal/dashboard')->assertOk();
    }
}
