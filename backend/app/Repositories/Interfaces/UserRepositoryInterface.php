<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): bool;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;

    public function getDoctors(int $institutionId, int $perPage = 15): LengthAwarePaginator;

    public function getByRole(int $institutionId, string $roleSlug): \Illuminate\Database\Eloquent\Collection;

    public function getActiveUsers(int $institutionId): \Illuminate\Database\Eloquent\Collection;

    public function updateLastLogin(int $id): bool;

    public function incrementFailedAttempts(int $id): int;

    public function resetFailedAttempts(int $id): bool;

    public function lockUser(int $id, int $minutes = 30): bool;

    public function unlockUser(int $id): bool;
}
