<?php

namespace App\Repositories\Interfaces;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RoomRepositoryInterface
{
    public function findById(int $id): ?Room;

    public function create(array $data): Room;

    public function update(int $id, array $data): Room;

    public function listByBuilding(int $buildingId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getAvailable(int $institutionId): Collection;

    public function getByType(int $institutionId, string $type): Collection;
}
