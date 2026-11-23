<?php

namespace Tests\Feature;

use App\Models\Institution;
use App\Models\Role;
use App\Models\User;
use App\Services\Integration\ApiKeyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('integrations.hospital_emr.webhook_verify_secret', 'test-verify-secret');
    }

    private function signedRequest(string $body, string $secret, ?string $timestamp = null): array
    {
        $timestamp ??= (string) time();

        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => 'sha256=' . hash_hmac(
                'sha256',
                $timestamp . '.' . $body,
                $secret
            ),
            'HTTP_X_WEBHOOK_TIMESTAMP' => $timestamp,
            'HTTP_X_WEBHOOK_ID' => 'evt-123',
            'HTTP_X_WEBHOOK_EVENT' => 'patient.updated',
        ];
    }

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
        ]);
    }

    public function test_valid_signed_webhook_is_stored(): void
    {
        $body = json_encode(['event' => 'patient.updated', 'data' => ['id' => 7]]);

        $response = $this->call(
            'POST',
            '/api/v1/webhooks/hospital-emr',
            [],
            [],
            [],
            $this->signedRequest($body, 'test-verify-secret'),
            $body
        );

        $response->assertCreated()->assertJsonPath('data.duplicate', false);
        $this->assertDatabaseHas('webhook_events', [
            'event' => 'patient.updated',
            'external_id' => 'evt-123',
            'status' => 'received',
        ]);
    }

    public function test_duplicate_webhook_is_not_stored_twice(): void
    {
        $body = json_encode(['id' => 'evt-123', 'event' => 'patient.updated']);
        $server = $this->signedRequest($body, 'test-verify-secret');

        $this->call('POST', '/api/v1/webhooks/hospital-emr', [], [], [], $server, $body)
            ->assertCreated();
        $this->call('POST', '/api/v1/webhooks/hospital-emr', [], [], [], $server, $body)
            ->assertOk()
            ->assertJsonPath('data.duplicate', true);

        $this->assertSame(1, \App\Models\WebhookEvent::count());
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $body = json_encode(['event' => 'patient.updated']);

        $this->call(
            'POST',
            '/api/v1/webhooks/hospital-emr',
            [],
            [],
            [],
            $this->signedRequest($body, 'wrong-secret'),
            $body
        )->assertUnauthorized();
    }

    public function test_old_timestamp_is_rejected(): void
    {
        $body = json_encode(['event' => 'patient.updated']);
        $server = $this->signedRequest($body, 'test-verify-secret', (string) (time() - 600));

        $this->call('POST', '/api/v1/webhooks/hospital-emr', [], [], [], $server, $body)
            ->assertUnauthorized();
    }

    public function test_admin_can_list_and_process_events(): void
    {
        $user = $this->superAdmin();
        $result = app(ApiKeyService::class)->create($user, 'Admin key');

        $event = \App\Models\WebhookEvent::create([
            'event' => 'patient.updated',
            'payload' => ['id' => 7],
            'received_at' => now(),
        ]);

        $this->withHeader('X-API-Key', $result['token'])
            ->getJson('/api/v1/webhooks/events')
            ->assertOk()
            ->assertJsonPath('meta.total', 1);

        $this->withHeader('X-API-Key', $result['token'])
            ->patchJson('/api/v1/webhooks/events/' . $event->id . '/processed')
            ->assertOk()
            ->assertJsonPath('data.status', 'processed');
    }
}
