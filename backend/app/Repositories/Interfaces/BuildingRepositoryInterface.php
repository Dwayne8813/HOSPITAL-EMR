<?php

namespace App\Repositories\Interfaces;

use App\Models\Building;
use Illuminate\Database\Eloquent\Collection;

interface BuildingRepositoryInterface
{
    public function findById(int $id): ?Building;

    public function create(array $data): Building;

    public function update(int $id, array $data): Building;

    public function delete(int $id): bool;

    public function list(int $institutionId, array $filters = []): Collection;

    public function getWithRooms(int $id): ?Building;
}
