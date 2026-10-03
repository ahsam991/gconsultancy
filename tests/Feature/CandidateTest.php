<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'candidate', 'guard_name' => 'web']);
    }

    public function test_candidate_can_be_created(): void
    {
        $user = User::factory()->create(['email' => 'candidate1@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'candidate1@example.com']);
        $this->assertNotNull($user->id);
    }

    public function test_candidate_can_be_read_and_updated(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);

        $this->assertEquals('Original Name', User::find($user->id)->name);

        $user->name = 'Updated Name';
        $user->save();

        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_candidate_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'dupe@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'dupe@example.com']);
    }

    public function test_duplicate_email_rejected_at_registration(): void
    {
        User::factory()->create(['email' => 'taken2@example.com']);

        $this->post('/auth/register', [
            'name' => 'Dupe',
            'email' => 'taken2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }
}
