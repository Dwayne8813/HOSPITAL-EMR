<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Accepts either a JWT bearer token (used by the web frontend) or a long-lived
 * API key (used by external systems). Both resolve to the same authenticated
 * user, so every existing controller keeps working unchanged.
 */
class AuthenticateApi
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        // An already authenticated session (or a test using actingAs) is valid.
        if (Auth::check()) {
            return $next($request);
        }

        $apiToken = $this->resolveApiToken($request);

        if ($apiToken instanceof ApiToken) {
            $user = $apiToken->user;

            if (!$user) {
                return $this->unauthorized('The API key owner no longer exists.');
            }

            if (!$user->is_active) {
                return $this->unauthorized(
                    'Your account has been deactivated. Please contact your administrator.',
                    403
                );
            }

            if (method_exists($user, 'is_locked') && $user->is_locked) {
                return $this->unauthorized(
                    'Your account is temporarily locked. Please try again later.',
                    403
                );
            }

            foreach ($abilities as $ability) {
                if (!$apiToken->hasAbility($ability)) {
                    return $this->unauthorized(
                        'This API key does not have the required ability: ' . $ability,
                        403
                    );
                }
            }

            $apiToken->forceFill(['last_used_at' => now()])->save();
            $request->attributes->set('api_token', $apiToken);
            Auth::setUser($user);

            return $next($request);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorized('Unauthenticated.');
            }

            Auth::setUser($user);

            return $next($request);
        } catch (JWTException $exception) {
            return $this->unauthorized('Unauthenticated.');
        }
    }

    private function resolveApiToken(Request $request): ?ApiToken
    {
        $plainToken = null;

        if ($request->hasHeader('X-API-Key')) {
            $plainToken = trim((string) $request->header('X-API-Key'));
        }

        if (!$plainToken) {
            $bearer = $request->bearerToken();

            // JWTs contain dots; API keys do not. This avoids parsing a key as
            // a JWT and lets systems that only support Bearer auth work too.
            if ($bearer && !str_contains($bearer, '.')) {
                $plainToken = trim($bearer);
            }
        }

        if (!$plainToken) {
            return null;
        }

        return ApiToken::with('user')
            ->where('token', ApiToken::hashToken($plainToken))
            ->active()
            ->first();
    }

    private function unauthorized(string $message, int $status = 401): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $status === 403 ? 'forbidden' : 'unauthenticated',
        ], $status);
    }
}
