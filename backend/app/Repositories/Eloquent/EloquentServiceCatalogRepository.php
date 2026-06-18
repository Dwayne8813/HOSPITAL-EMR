<?php

namespace App\Repositories\Eloquent;

use App\Models\ServiceCatalog;
use App\Repositories\Interfaces\ServiceCatalogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentServiceCatalogRepository implements ServiceCatalogRepositoryInterface
{
    public function __construct(
        protected ServiceCatalog $model,
    ) {}

    public function findById(int $id): ?ServiceCatalog
    {
        return $this->model->with('institution')->find($id);
    }

    public function create(array $data): ServiceCatalog
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ServiceCatalog
    {
        $service = $this->model->findOrFail($id);
        $service->update($data);

        return $service;
    }

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId);

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
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
            ->search($query)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getByCategory(int $institutionId, string $category, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byCategory($category)
            ->orderBy('name')
            ->paginate($perPage);
    }
}
