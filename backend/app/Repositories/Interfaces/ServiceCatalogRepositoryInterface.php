<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ServiceCatalogRepositoryInterface
{
    public function findById(int $id): ?ServiceCatalog;

    public function create(array $data): ServiceCatalog;

    public function update(int $id, array $data): ServiceCatalog;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;

    public function getByCategory(int $institutionId, string $category, int $perPage = 15): LengthAwarePaginator;
}
