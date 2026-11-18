<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account deactivated. Please contact your administrator.',
            ], 403);
        }

        if ($user->locked_until && now()->lt($user->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'Account locked. Please try again later.',
            ], 403);
        }

        $token = JWTAuth::fromUser($user);

        $user->update([
            'last_login' => now(),
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'institution_id' => $user->institution_id,
                    'is_active' => $user->is_active,
                ],
            ],
            'message' => 'Sign in successful.',
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
        } catch (\Exception $e) {
            // Token already invalid
        }

        return response()->json([
            'success' => true,
            'message' => 'Signed out successfully.',
        ], 200);
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            $user = JWTAuth::parseToken()->toUser();

            return response()->json([
                'success' => true,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
                'message' => 'Token refreshed successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing token.',
            ], 401);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'institution_id' => $request->input('institution_id', 1),
            'role_id' => $request->input('role_id'),
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'user' => $user,
            ],
            'message' => 'Registration successful.',
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'institution_id' => $user->institution_id,
                'is_active' => $user->is_active,
                'last_login' => $user->last_login,
                'created_at' => $user->created_at,
            ],
            'message' => 'Profile retrieved.',
        ], 200);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ], 200);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(60);
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'If the email exists, you will receive a recovery link.',
        ], 200);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($request->password)]);
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $request->email)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
        ], 200);
    }

    public function enableMfa(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'MFA not available in this version.',
        ], 200);
    }

    public function verifyMfa(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'MFA not available in this version.',
        ], 200);
    }
}
