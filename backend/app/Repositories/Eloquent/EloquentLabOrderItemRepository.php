<?php

namespace App\Repositories\Eloquent;

use App\Models\LabOrderItem;
use App\Repositories\Interfaces\LabOrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentLabOrderItemRepository implements LabOrderItemRepositoryInterface
{
    public function __construct(
        protected LabOrderItem $model,
    ) {}

    public function findById(int $id): ?LabOrderItem
    {
        return $this->model->with('labOrder', 'results')->find($id);
    }

    public function create(array $data): LabOrderItem
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): LabOrderItem
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);

        return $item;
    }

    public function listByLabOrder(int $labOrderId): Collection
    {
        return $this->model
            ->where('lab_order_id', $labOrderId)
            ->with('results')
            ->get();
    }

    public function addResults(int $id, array $results): LabOrderItem
    {
        $item = $this->model->findOrFail($id);
        $item->update(['status' => 'completed']);

        foreach ($results as $result) {
            $item->results()->create($result);
        }

        return $item->fresh('results');
    }

    public function validate(int $id, int $validatedBy, ?string $notes = null): LabOrderItem
    {
        $item = $this->model->findOrFail($id);
        $item->update([
            'status' => 'validated',
            'validated_by' => $validatedBy,
            'validation_notes' => $notes,
        ]);

        return $item;
    }
}
