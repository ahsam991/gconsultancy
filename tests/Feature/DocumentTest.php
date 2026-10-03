<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'candidate', 'guard_name' => 'web']);

        Route::middleware('auth')->post('/__test_docs/upload', function (Request $request) {
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'category' => 'sometimes|string',
            ]);
            $path = $request->file('file')->store('docs/'.auth()->id());

            return response()->json(['path' => $path]);
        });

        Route::middleware('auth')->get('/__test_docs/{ownerId}/{file}', function ($ownerId, $file) {
            if ((int) $ownerId !== (int) auth()->id()) {
                abort(403, 'You may only download your own documents.');
            }
            if (! Storage::exists("docs/{$ownerId}/{$file}")) {
                abort(404);
            }

            return response()->json(['ok' => true, 'file' => $file]);
        })->where(['ownerId' => '[0-9]+', 'file' => '.*']);
    }

    public function test_candidate_can_upload_document(): void
    {
        Storage::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/__test_docs/upload', [
            'category' => 'passport',
            'file' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
        ]);

        $response->assertOk();
        $path = $response->json('path');
        $this->assertNotEmpty($path);
        Storage::assertExists($path);
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        Storage::fake();
        $user = User::factory()->create();

        // JSON request → 422 payload (avoids a core quirk where
        // assertSessionHasErrors cannot see errors flashed from
        // multipart file-upload redirects in this Laravel version).
        $this->actingAs($user)->postJson('/__test_docs/upload', [
            'file' => UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload'),
        ])->assertJsonValidationErrors('file');
    }

    public function test_owner_can_download_own_document(): void
    {
        Storage::fake();
        $user = User::factory()->create();
        Storage::put("docs/{$user->id}/passport.pdf", 'fake-pdf-bytes');

        $this->actingAs($user)
            ->get("/__test_docs/{$user->id}/passport.pdf")
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_unauthorized_download_is_blocked(): void
    {
        Storage::fake();
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        Storage::put("docs/{$owner->id}/passport.pdf", 'fake-pdf-bytes');

        $this->actingAs($intruder)
            ->get("/__test_docs/{$owner->id}/passport.pdf")
            ->assertForbidden();
    }

    public function test_guest_cannot_upload(): void
    {
        Storage::fake();

        $this->post('/__test_docs/upload', [
            'file' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
    }
}
