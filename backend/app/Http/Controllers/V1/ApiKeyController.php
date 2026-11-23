<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use App\Services\Integration\ApiKeyService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function __construct(private readonly ApiKeyService $apiKeyService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = ApiToken::with('user:id,name,email,role_id');

        if (!$this->isAdmin()) {
            $query->forUser(auth()->id());
        } elseif (auth()->user()?->role?->slug !== 'super_admin') {
            $query->whereHas('user', function ($q) {
                $q->where('institution_id', auth()->user()->institution_id);
            });
        }

        $tokens = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
            'message' => 'API keys retrieved successfully.',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'abilities' => ['sometimes', 'array'],
            'abilities.*' => ['string', 'max:100'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $userId = $validated['user_id'] ?? auth()->id();

        if ((int) $userId !== (int) auth()->id() && !$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can create API keys for other users.',
            ], 403);
        }

        $user = User::findOrFail($userId);

        if ($this->isAdmin() && auth()->user()?->role?->slug !== 'super_admin'
            && $user->institution_id !== auth()->user()->institution_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot create API keys for users in another institution.',
            ], 403);
        }

        $result = $this->apiKeyService->create(
            $user,
            $validated['name'],
            $validated['abilities'] ?? ['*'],
            !empty($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $result['api_token']->id,
                'name' => $result['api_token']->name,
                'user_id' => $user->id,
                'abilities' => $result['api_token']->abilities,
                'expires_at' => $result['api_token']->expires_at,
                // Shown exactly once. It is a SHA-256 hash in the database.
                'token' => $result['token'],
            ],
            'message' => 'API key created. Copy the token now; it cannot be shown again.',
        ], 201);
    }

    public function destroy(ApiToken $apiKey): JsonResponse
    {
        if (!$this->isAdmin() && (int) $apiKey->user_id !== (int) auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only revoke your own API keys.',
            ], 403);
        }

        $apiKey->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'API key revoked successfully.',
        ]);
    }

    private function isAdmin(): bool
    {
        return in_array(auth()->user()?->role?->slug, ['super_admin', 'institution_admin'], true);
    }
}
