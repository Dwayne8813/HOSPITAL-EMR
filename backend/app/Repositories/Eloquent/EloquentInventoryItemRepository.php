<?php

namespace App\Repositories\Eloquent;

use App\Models\InventoryItem;
use App\Repositories\Interfaces\InventoryItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentInventoryItemRepository implements InventoryItemRepositoryInterface
{
    public function __construct(
        protected InventoryItem $model,
    ) {}

    public function findById(int $id): ?InventoryItem
    {
        return $this->model->with('institution', 'category')->find($id);
    }

    public function create(array $data): InventoryItem
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): InventoryItem
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);

        return $item;
    }

    public function delete(int $id): bool
    {
        $item = $this->model->findOrFail($id);

        return $item->delete();
    }

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId)->with('category');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
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
            ->with('category')
            ->search($query)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getLowStock(int $institutionId): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->active()
            ->lowStock()
            ->with('category')
            ->get();
    }

    public function getByCategory(int $institutionId, int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->where('category_id', $categoryId)
            ->with('category')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
