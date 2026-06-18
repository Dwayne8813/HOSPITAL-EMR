<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'unauthenticated',
            ], 401);
        }

        if (method_exists($user, 'is_active') && !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Your account has been deactivated. Please contact your administrator.',
                'error' => 'account_deactivated',
            ], 403);
        }

        if (method_exists($user, 'is_locked') && $user->is_locked) {
            return response()->json([
                'message' => 'Your account has been locked. Please contact your administrator.',
                'error' => 'account_locked',
            ], 403);
        }

        if (method_exists($user, 'locked_until') && $user->locked_until && now()->lt($user->locked_until)) {
            $minutesRemaining = now()->diffInMinutes($user->locked_until);

            return response()->json([
                'message' => "Your account is temporarily locked. Please try again in {$minutesRemaining} minutes.",
                'error' => 'account_temporarily_locked',
                'locked_until' => $user->locked_until->toIso8601String(),
                'minutes_remaining' => $minutesRemaining,
            ], 403);
        }

        if (method_exists($user, 'email_verified_at') && !$user->email_verified_at && config('auth.requires_verification', false)) {
            return response()->json([
                'message' => 'Please verify your email address before continuing.',
                'error' => 'email_not_verified',
            ], 403);
        }

        $request->attributes->set('current_user', $user);

        return $next($request);
    }
}
