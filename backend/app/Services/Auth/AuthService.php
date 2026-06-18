<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\LoginHistory;
use App\Models\PasswordHistory;
use App\Models\Session;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function login(LoginDTO $dto): array
    {
        $institution = Institution::where('slug', $dto->institutionSlug)->first();

        if (!$institution) {
            throw new \App\Exceptions\AuthenticationException('Institution not found.');
        }

        $user = User::where('email', $dto->email)
            ->where('institution_id', $institution->id)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            $this->logLoginAttempt($dto->email, $institution->id, false);
            throw new \App\Exceptions\AuthenticationException('Invalid credentials.');
        }

        if (!$user->is_active) {
            throw new \App\Exceptions\AuthenticationException('Account is deactivated.');
        }

        if ($user->locked_until && $user->locked_until->isFuture()) {
            throw new \App\Exceptions\AuthenticationException(
                'Account is locked until ' . $user->locked_until->format('Y-m-d H:i:s')
            );
        }

        if ($user->mfa_enabled) {
            $pendingToken = JWTAuth::claims([
                'pending_mfa' => true,
                'user_id' => $user->id,
            ])->temporaryToken();

            return [
                'requires_mfa' => true,
                'pending_token' => $pendingToken,
                'message' => 'MFA verification required.',
            ];
        }

        $token = JWTAuth::fromUser($user);

        $user->update(['last_login' => now()]);
        $this->logLoginAttempt($dto->email, $institution->id, true);
        $this->createSession($user, $token);

        return [
            'requires_mfa' => false,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user->load(['role', 'institution', 'specialty']),
        ];
    }

    public function logout(User $user): void
    {
        $user->sessions()->where('is_active', true)->update(['is_active' => false]);
        JWTAuth::invalidate(JWTAuth::parse(JWTAuth::getToken()));
        $this->logAudit($user, 'logout', 'User logged out successfully.');
    }

    public function refreshToken(User $user): array
    {
        $token = JWTAuth::fromUser($user);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];
    }

    public function register(RegisterDTO $dto): User
    {
        $institution = Institution::where('slug', $dto->institutionSlug)->first();

        if (!$institution) {
            throw new \App\Exceptions\AuthenticationException('Institution not found.');
        }

        if (User::where('email', $dto->email)->where('institution_id', $institution->id)->exists()) {
            throw new \App\Exceptions\AuthenticationException('Email already registered in this institution.');
        }

        $user = User::create([
            'institution_id' => $institution->id,
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'is_active' => true,
        ]);

        $this->logAudit($user, 'register', 'New user registered.');

        return $user->load('role');
    }

    public function forgotPassword(string $email): string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return 'If the email exists, a reset link has been sent.';
        }

        $token = Str::random(64);
        PasswordHistory::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(2),
        ]);

        return $token;
    }

    public function resetPassword(string $token, string $email, string $password): void
    {
        $reset = PasswordHistory::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            throw new \App\Exceptions\AuthenticationException('Invalid or expired reset token.');
        }

        $user = User::where('email', $email)->where('id', $reset->user_id)->first();

        if (!$user) {
            throw new \App\Exceptions\AuthenticationException('Invalid email for this token.');
        }

        $user->update(['password' => $password]);
        $reset->update(['used_at' => now()]);

        PasswordHistory::where('user_id', $user->id)
            ->where('id', '!=', $reset->id)
            ->delete();

        $this->logAudit($user, 'password_reset', 'Password has been reset.');
    }

    public function verifyEmail(User $user): void
    {
        $user->update(['email_verified_at' => now()]);
        $this->logAudit($user, 'email_verified', 'Email address verified.');
    }

    public function enableMfa(User $user): array
    {
        $secret = \Pragmarx\Google2FA\Google2FA::generateSecretKey();
        $user->update(['mfa_secret' => $secret]);

        $qrCodeUrl = \Pragmarx\Google2FA\Google2FA::getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    public function verifyMfa(User $user, string $code): bool
    {
        $google2fa = new \Pragmarx\Google2FA\Google2FA();

        $valid = $google2fa->verifyKey($user->mfa_secret, $code);

        if ($valid && !$user->mfa_enabled) {
            $user->update(['mfa_enabled' => true]);
            $this->logAudit($user, 'mfa_enabled', 'Multi-factor authentication enabled.');
        }

        return $valid;
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): void
    {
        if (!Hash::check($oldPassword, $user->password)) {
            throw new \App\Exceptions\AuthenticationException('Current password is incorrect.');
        }

        $user->update(['password' => $newPassword]);
        $this->logAudit($user, 'password_changed', 'Password changed successfully.');

        JWTAuth::invalidate(JWTAuth::parse(JWTAuth::getToken()));
    }

    public function getActiveSessions(User $user): \Illuminate\Support\Collection
    {
        return $user->sessions()
            ->where('is_active', true)
            ->orderByDesc('last_activity')
            ->get();
    }

    public function lockAccount(User $user, int $minutes = 30): void
    {
        $user->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
        $this->logAudit($user, 'account_locked', "Account locked for {$minutes} minutes.");
    }

    public function unlockAccount(User $user): void
    {
        $user->update([
            'locked_until' => null,
            'failed_attempts' => 0,
        ]);
        $this->logAudit($user, 'account_unlocked', 'Account unlocked.');
    }

    private function logLoginAttempt(string $email, int $institutionId, bool $success): void
    {
        LoginHistory::create([
            'email' => $email,
            'institution_id' => $institutionId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'success' => $success,
            'attempted_at' => now(),
        ]);
    }

    private function createSession(User $user, string $token): void
    {
        $payload = JWTAuth::setToken($token)->getPayload();
        $jti = $payload->get('jti');

        Session::create([
            'user_id' => $user->id,
            'institution_id' => $user->institution_id,
            'token_id' => $jti,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_active' => true,
            'last_activity' => now(),
        ]);
    }

    private function logAudit(User $user, string $action, string $description): void
    {
        AuditLog::create([
            'institution_id' => $user->institution_id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);
    }
}
