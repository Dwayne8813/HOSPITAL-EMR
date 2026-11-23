<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Institution;
use App\Models\Role;
use App\Models\User;
use App\Services\Integration\ApiKeyService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyAuthTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $institution = Institution::factory()->create();
        $role = Role::factory()->create([
            'institution_id' => $institution->id,
            'slug' => 'super_admin',
        ]);

        return User::factory()->create([
            'institution_id' => $institution->id,
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_api_key_authenticates_protected_endpoint(): void
    {
        $user = $this->superAdmin();
        $result = app(ApiKeyService::class)->create($user, 'Integration test');

        $response = $this->withHeader('X-API-Key', $result['token'])
            ->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_api_key_is_accepted_as_bearer_token(): void
    {
        $result = app(ApiKeyService::class)->create($this->superAdmin(), 'Bearer test');

        $this->withHeader('Authorization', 'Bearer ' . $result['token'])
            ->getJson('/api/v1/auth/me')
            ->assertOk();
    }

    public function test_invalid_api_key_is_rejected(): void
    {
        $this->withHeader('X-API-Key', 'emr_not-a-real-key')
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_expired_api_key_is_rejected(): void
    {
        $user = $this->superAdmin();
        $result = app(ApiKeyService::class)->create(
            $user,
            'Expired key',
            ['*'],
            Carbon::now()->subDay()
        );

        $this->withHeader('X-API-Key', $result['token'])
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }

    public function test_key_is_hashed_in_database_and_plaintext_returned_once(): void
    {
        $user = $this->superAdmin();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/api-keys', [
                'name' => 'Hospital EMR Integration',
                'abilities' => ['patients.view'],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Hospital EMR Integration')
            ->assertJsonStructure(['data' => ['token']]);

        $plaintext = $response->json('data.token');
        $this->assertStringStartsWith('emr_', $plaintext);
        $this->assertDatabaseHas('api_tokens', [
            'token' => ApiToken::hashToken($plaintext),
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('api_tokens', ['token' => $plaintext]);
    }

    public function test_api_key_can_be_revoked(): void
    {
        $user = $this->superAdmin();
        $result = app(ApiKeyService::class)->create($user, 'To revoke');

        $this->actingAs($user)
            ->deleteJson('/api/v1/api-keys/' . $result['api_token']->id)
            ->assertOk();

        // Drop the actingAs session so the next request relies on the key only.
        \Illuminate\Support\Facades\Auth::forgetGuards();

        $this->withHeader('X-API-Key', $result['token'])
            ->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    }
}
