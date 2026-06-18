<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiter as RateLimiterContract;

class RateLimitMiddleware
{
    private const MAX_ATTEMPTS_GLOBAL = 60;
    private const MAX_ATTEMPTS_LOGIN = 5;
    private const MAX_ATTEMPTS_API = 120;
    private const MAX_ATTEMPTS_HEAVY = 10;

    private const DECAY_MINUTES_GLOBAL = 1;
    private const DECAY_MINUTES_LOGIN = 1;
    private const DECAY_MINUTES_API = 1;
    private const DECAY_MINUTES_HEAVY = 1;

    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        $rateLimitConfig = $this->getRateLimitConfig($limiter);
        $maxAttempts = $rateLimitConfig['max_attempts'];
        $decayMinutes = $rateLimitConfig['decay_minutes'];
        $key = $this->resolveRateLimitKey($request, $limiter);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);

            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'error' => 'rate_limit_exceeded',
                'retry_after' => $retryAfter,
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
                'Retry-After' => $retryAfter,
            ]);
        }

        $attempts = RateLimiter::attempts($key);
        $remaining = max(0, $maxAttempts - $attempts - 1);

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => now()->addMinutes($decayMinutes)->getTimestamp(),
        ]);
    }

    private function getRateLimitConfig(string $limiter): array
    {
        return match ($limiter) {
            'login' => [
                'max_attempts' => self::MAX_ATTEMPTS_LOGIN,
                'decay_minutes' => self::DECAY_MINUTES_LOGIN,
            ],
            'heavy' => [
                'max_attempts' => self::MAX_ATTEMPTS_HEAVY,
                'decay_minutes' => self::DECAY_MINUTES_HEAVY,
            ],
            'global' => [
                'max_attempts' => self::MAX_ATTEMPTS_GLOBAL,
                'decay_minutes' => self::DECAY_MINUTES_GLOBAL,
            ],
            default => [
                'max_attempts' => self::MAX_ATTEMPTS_API,
                'decay_minutes' => self::DECAY_MINUTES_API,
            ],
        };
    }

    private function resolveRateLimitKey(Request $request, string $limiter): string
    {
        $userId = $request->user()?->id;
        $institutionId = $request->input('institution_id') ?? $request->header('X-Institution-Id');
        $ip = $request->ip();

        $prefix = match ($limiter) {
            'login' => 'login',
            'heavy' => 'heavy',
            'global' => 'global',
            default => 'api',
        };

        if ($userId && $institutionId) {
            return "{$prefix}:{$institutionId}:{$userId}";
        }

        if ($userId) {
            return "{$prefix}:{$userId}";
        }

        return "{$prefix}:{$ip}";
    }
}
