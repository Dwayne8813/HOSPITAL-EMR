<?php

namespace App\Repositories\Interfaces;

use App\Models\InventoryItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InventoryItemRepositoryInterface
{
    public function findById(int $id): ?InventoryItem;

    public function create(array $data): InventoryItem;

    public function update(int $id, array $data): InventoryItem;

    public function delete(int $id): bool;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;

    public function getLowStock(int $institutionId): Collection;

    public function getByCategory(int $institutionId, int $categoryId, int $perPage = 15): LengthAwarePaginator;
}
