<?php

namespace App\Repositories\Eloquent;

use App\Models\InventoryMovement;
use App\Repositories\Interfaces\InventoryMovementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentInventoryMovementRepository implements InventoryMovementRepositoryInterface
{
    public function __construct(
        protected InventoryMovement $model,
    ) {}

    public function findById(int $id): ?InventoryMovement
    {
        return $this->model->with('institution', 'inventoryItem', 'medication', 'performedBy')->find($id);
    }

    public function create(array $data): InventoryMovement
    {
        return $this->model->create($data);
    }

    public function listByItem(int $itemId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('inventory_item_id', $itemId)
            ->with('performedBy')
            ->orderBy('performed_at', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('inventoryItem', 'medication', 'performedBy');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['inventory_item_id'])) {
            $query->where('inventory_item_id', $filters['inventory_item_id']);
        }

        if (!empty($filters['medication_id'])) {
            $query->where('medication_id', $filters['medication_id']);
        }

        return $query->orderBy('performed_at', 'desc')->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('performed_at', [$from, $to])
            ->with('inventoryItem', 'medication', 'performedBy')
            ->orderBy('performed_at', 'desc')
            ->paginate($perPage);
    }

    public function getByType(int $institutionId, string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byType($type)
            ->with('inventoryItem', 'medication', 'performedBy')
            ->orderBy('performed_at', 'desc')
            ->paginate($perPage);
    }
}
