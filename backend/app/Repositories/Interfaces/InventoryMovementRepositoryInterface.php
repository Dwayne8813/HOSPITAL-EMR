<?php

namespace App\Repositories\Interfaces;

use App\Models\InventoryMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InventoryMovementRepositoryInterface
{
    public function findById(int $id): ?InventoryMovement;

    public function create(array $data): InventoryMovement;

    public function listByItem(int $itemId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function getByType(int $institutionId, string $type, int $perPage = 15): LengthAwarePaginator;
}
