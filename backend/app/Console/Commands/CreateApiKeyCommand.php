<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Integration\ApiKeyService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use InvalidArgumentException;

class CreateApiKeyCommand extends Command
{
    protected $signature = 'api-key:create
                            {name : A label for the key (e.g. Hospital EMR Integration)}
                            {--user= : User ID or email that owns the key (defaults to first super admin)}
                            {--ability=* : Ability to grant, repeatable (defaults to *)}
                            {--expires= : Expiration date, e.g. 2027-01-01 or +30 days (defaults to never)}';

    protected $description = 'Create a long-lived API key for system integrations';

    public function handle(ApiKeyService $apiKeyService): int
    {
        $user = $this->resolveUser();

        if (!$user) {
            $this->error('No matching user found. Use --user=<id or email>.');

            return self::FAILURE;
        }

        $abilities = $this->option('ability') ?: ['*'];
        $expiresAt = null;

        if ($this->option('expires')) {
            try {
                $expiresAt = Carbon::parse($this->option('expires'));
            } catch (InvalidArgumentException) {
                $this->error('Invalid expiration date.');

                return self::FAILURE;
            }

            if ($expiresAt->isPast()) {
                $this->error('Expiration date must be in the future.');

                return self::FAILURE;
            }
        }

        $result = $apiKeyService->create(
            $user,
            $this->argument('name'),
            $abilities,
            $expiresAt
        );

        $this->newLine();
        $this->info('API key created. Copy it now — it cannot be shown again.');
        $this->line('Owner:    ' . $user->email);
        $this->line('Name:     ' . $result['api_token']->name);
        $this->line('Abilities: ' . implode(', ', $abilities));
        $this->newLine();
        $this->line($result['token']);
        $this->newLine();
        $this->comment('Use it as: X-API-Key: <token>  or  Authorization: Bearer <token>');

        return self::SUCCESS;
    }

    private function resolveUser(): ?User
    {
        $identifier = $this->option('user');

        if ($identifier) {
            return User::query()
                ->when(
                    is_numeric($identifier),
                    fn ($q) => $q->where('id', $identifier),
                    fn ($q) => $q->where('email', $identifier)
                )
                ->first();
        }

        return User::query()
            ->whereHas('role', fn ($q) => $q->where('slug', 'super_admin'))
            ->first();
    }
}
