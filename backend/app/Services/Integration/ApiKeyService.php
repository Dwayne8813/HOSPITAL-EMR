<?php

declare(strict_types=1);

namespace App\Services\Integration;

use App\Models\ApiToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ApiKeyService
{
    /**
     * Create a long-lived API key. The plaintext is returned only once;
     * the database stores a SHA-256 hash and never the secret itself.
     *
     * @param  array<int, string>  $abilities
     * @return array{token: string, api_token: ApiToken}
     */
    public function create(
        User $user,
        string $name,
        array $abilities = ['*'],
        ?Carbon $expiresAt = null
    ): array {
        $plainToken = 'emr_' . Str::random(64);

        $apiToken = ApiToken::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => ApiToken::hashToken($plainToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return [
            'token' => $plainToken,
            'api_token' => $apiToken,
        ];
    }
}
