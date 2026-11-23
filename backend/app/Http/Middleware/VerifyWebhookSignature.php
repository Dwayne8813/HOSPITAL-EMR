<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies HMAC-SHA256 signatures on inbound webhooks.
 *
 * Expected header: X-Webhook-Signature: sha256=<hex digest>
 * When X-Webhook-Timestamp is present the signed payload is
 * "<timestamp>.<raw body>" and the timestamp must be within five minutes.
 */
class VerifyWebhookSignature
{
    private const TOLERANCE_SECONDS = 300;

    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('integrations.hospital_emr.webhook_verify_secret');

        if (!is_string($secret) || $secret === '') {
            return $this->error('Webhook verification secret is not configured.', 503);
        }

        $signature = $request->header('X-Webhook-Signature')
            ?: $request->header('X-Signature');

        if (!$signature) {
            return $this->error('Missing webhook signature.', 401);
        }

        $timestamp = $request->header('X-Webhook-Timestamp');
        $rawBody = $request->getContent();
        $signedPayload = $timestamp ? $timestamp . '.' . $rawBody : $rawBody;

        $expected = 'sha256=' . hash_hmac('sha256', $signedPayload, $secret);
        $provided = str_starts_with($signature, 'sha256=')
            ? $signature
            : 'sha256=' . $signature;

        if (!hash_equals($expected, $provided)) {
            return $this->error('Invalid webhook signature.', 401);
        }

        if ($timestamp !== null) {
            if (!ctype_digit((string) $timestamp)) {
                return $this->error('Invalid webhook timestamp.', 401);
            }

            if (abs(time() - (int) $timestamp) > self::TOLERANCE_SECONDS) {
                return $this->error('Webhook timestamp is outside the allowed window.', 401);
            }
        }

        $request->attributes->set('webhook_raw_payload', $rawBody);
        $request->attributes->set('webhook_signature', $signature);

        return $next($request);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
