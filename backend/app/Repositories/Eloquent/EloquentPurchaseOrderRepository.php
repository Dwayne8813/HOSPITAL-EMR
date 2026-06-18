<?php

namespace App\Repositories\Eloquent;

use App\Models\PurchaseOrder;
use App\Repositories\Interfaces\PurchaseOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function __construct(
        protected PurchaseOrder $model,
    ) {}

    public function findById(int $id): ?PurchaseOrder
    {
        return $this->model->with('institution', 'supplier', 'creator', 'items')->find($id);
    }

    public function create(array $data): PurchaseOrder
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PurchaseOrder
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);

        return $order;
    }

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('supplier', 'creator');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('ordered_at', 'desc')->paginate($perPage);
    }

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->where('status', $status)
            ->with('supplier', 'creator')
            ->orderBy('ordered_at', 'desc')
            ->paginate($perPage);
    }

    public function receiveOrder(int $id): PurchaseOrder
    {
        $order = $this->model->findOrFail($id);
        $order->update([
            'status' => 'received',
            'received_at' => now(),
        ]);

        foreach ($order->items as $item) {
            if ($item->medication_id) {
                $medication = \App\Models\Medication::find($item->medication_id);
                if ($medication) {
                    $medication->increment('current_stock', $item->quantity);
                }
            }
        }

        return $order->fresh('items');
    }
}
