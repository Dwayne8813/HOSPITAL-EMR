<?php

namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;

    public function create(array $data): Supplier;

    public function update(int $id, array $data): Supplier;

    public function delete(int $id): bool;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;
}
