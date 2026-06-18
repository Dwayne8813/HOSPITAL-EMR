<?php

namespace App\Repositories\Interfaces;

use App\Models\Bed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BedRepositoryInterface
{
    public function findById(int $id): ?Bed;

    public function create(array $data): Bed;

    public function update(int $id, array $data): Bed;

    public function listByRoom(int $roomId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getAvailable(int $institutionId): Collection;

    public function getByStatus(int $institutionId, string $status): Collection;

    public function getOccupancyMap(int $institutionId): array;
}
