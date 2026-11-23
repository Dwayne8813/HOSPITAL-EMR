<?php

declare(strict_types=1);

namespace App\Services\Integration;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Sends signed webhooks to the configured external system.
 */
class WebhookService
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, status: int|null, id: string, error: string|null}
     */
    public function send(string $event, array $payload, ?string $url = null): array
    {
        $url = $url ?: config('integrations.hospital_emr.webhook_url');

        if (!$url) {
            throw new RuntimeException('Webhook URL is not configured.');
        }

        $id = (string) Str::uuid();
        $timestamp = (string) time();
        $secret = (string) config('integrations.hospital_emr.webhook_signing_secret');

        $body = json_encode([
            'id' => $id,
            'event' => $event,
            'created_at' => now()->toIso8601String(),
            'data' => $payload,
        ], JSON_THROW_ON_ERROR);

        // The exact same string is signed and sent so the receiver can verify
        // it without re-encoding JSON (which could change key order/escaping).
        $signature = 'sha256=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Webhook-Id' => $id,
            'X-Webhook-Event' => $event,
            'X-Webhook-Timestamp' => $timestamp,
            'X-Webhook-Signature' => $signature,
        ];

        $inboundKey = config('integrations.hospital_emr.outbound_api_key');

        if ($inboundKey) {
            $headers['X-API-Key'] = $inboundKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->withBody($body, 'application/json')
                ->post($url);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'id' => $id,
                'error' => $response->successful() ? null : $response->body(),
            ];
        } catch (ConnectionException $exception) {
            return [
                'success' => false,
                'status' => null,
                'id' => $id,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
