<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use App\Services\Integration\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WebhookController extends Controller
{
    public function __construct(private readonly WebhookService $webhookService)
    {
    }

    /**
     * Public endpoint. Signature is verified by the webhook.signature middleware.
     */
    public function receive(Request $request): JsonResponse
    {
        $raw = $request->attributes->get('webhook_raw_payload') ?: $request->getContent();
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook payload must be valid JSON.',
            ], 422);
        }

        $event = $request->header('X-Webhook-Event')
            ?: ($payload['event'] ?? 'unknown');
        $externalId = $request->header('X-Webhook-Id')
            ?: ($payload['id'] ?? null);

        if ($externalId) {
            $existing = WebhookEvent::where('source', 'hospital_emr')
                ->where('external_id', (string) $externalId)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'data' => ['id' => $existing->id, 'duplicate' => true],
                    'message' => 'Webhook already received.',
                ]);
            }
        }

        $webhookEvent = WebhookEvent::create([
            'source' => 'hospital_emr',
            'event' => (string) $event,
            'external_id' => $externalId ? (string) $externalId : null,
            'payload' => $payload,
            'headers' => [
                'x_webhook_id' => $request->header('X-Webhook-Id'),
                'x_webhook_event' => $request->header('X-Webhook-Event'),
                'x_webhook_timestamp' => $request->header('X-Webhook-Timestamp'),
                'user_agent' => $request->userAgent(),
            ],
            'signature' => $request->attributes->get('webhook_signature'),
            'status' => 'received',
            'received_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $webhookEvent->id, 'duplicate' => false],
            'message' => 'Webhook received.',
        ], 201);
    }

    public function events(Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return $this->forbidden();
        }

        $query = WebhookEvent::query()
            ->when($request->input('status'), fn ($q, $status) => $q->byStatus($status))
            ->when($request->input('event'), fn ($q, $event) => $q->byEvent($event))
            ->orderByDesc('received_at');

        $events = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'message' => 'Webhook events retrieved successfully.',
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    public function show(WebhookEvent $webhookEvent): JsonResponse
    {
        if (!$this->isAdmin()) {
            return $this->forbidden();
        }

        return response()->json([
            'success' => true,
            'data' => $webhookEvent,
        ]);
    }

    public function markProcessed(WebhookEvent $webhookEvent): JsonResponse
    {
        if (!$this->isAdmin()) {
            return $this->forbidden();
        }

        $webhookEvent->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $webhookEvent,
            'message' => 'Webhook marked as processed.',
        ]);
    }

    public function test(Request $request): JsonResponse
    {
        if (!$this->isAdmin()) {
            return $this->forbidden();
        }

        $validated = $request->validate([
            'event' => ['sometimes', 'string', 'max:100'],
            'data' => ['sometimes', 'array'],
        ]);

        try {
            $result = $this->webhookService->send(
                $validated['event'] ?? 'integration.test',
                $validated['data'] ?? ['message' => 'Test webhook from Hospital EMR']
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 503);
        }

        return response()->json([
            'success' => $result['success'],
            'data' => $result,
            'message' => $result['success']
                ? 'Test webhook delivered.'
                : 'Test webhook delivery failed.',
        ], $result['success'] ? 200 : 502);
    }

    private function isAdmin(): bool
    {
        return in_array(auth()->user()?->role?->slug, ['super_admin', 'institution_admin'], true);
    }

    private function forbidden(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to manage webhooks.',
        ], 403);
    }
}
