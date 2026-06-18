<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model,
    ) {}

    public function findById(int $id): ?User
    {
        return $this->model->with('role', 'specialty')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);

        return $user->fresh(['role', 'specialty']);
    }

    public function delete(int $id): bool
    {
        $user = $this->model->findOrFail($id);

        return $user->delete();
    }

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId)->with('role', 'specialty');

        if (!empty($filters['role'])) {
            $query->whereHas('role', fn ($q) => $q->where('slug', $filters['role']));
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('role', 'specialty')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getDoctors(int $institutionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->doctors()
            ->with('specialty')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getByRole(int $institutionId, string $roleSlug): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereHas('role', fn ($q) => $q->where('slug', $roleSlug))
            ->get();
    }

    public function getActiveUsers(int $institutionId): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->active()
            ->with('role')
            ->get();
    }

    public function updateLastLogin(int $id): bool
    {
        return (bool) $this->model->where('id', $id)->update([
            'last_login' => now(),
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function incrementFailedAttempts(int $id): int
    {
        $user = $this->model->findOrFail($id);
        $user->increment('failed_attempts');

        return $user->fresh()->failed_attempts;
    }

    public function resetFailedAttempts(int $id): bool
    {
        return (bool) $this->model->where('id', $id)->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function lockUser(int $id, int $minutes = 30): bool
    {
        return (bool) $this->model->where('id', $id)->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    public function unlockUser(int $id): bool
    {
        return (bool) $this->model->where('id', $id)->update([
            'locked_until' => null,
            'failed_attempts' => 0,
        ]);
    }
}
