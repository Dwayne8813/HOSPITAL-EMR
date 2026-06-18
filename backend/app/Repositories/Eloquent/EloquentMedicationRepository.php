<?php

namespace App\Repositories\Eloquent;

use App\Models\Medication;
use App\Repositories\Interfaces\MedicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentMedicationRepository implements MedicationRepositoryInterface
{
    public function __construct(
        protected Medication $model,
    ) {}

    public function findById(int $id): ?Medication
    {
        return $this->model->with('institution', 'category', 'catalog')->find($id);
    }

    public function create(array $data): Medication
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Medication
    {
        $medication = $this->model->findOrFail($id);
        $medication->update($data);

        return $medication;
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

        if (isset($filters['is_controlled'])) {
            $query->where('is_controlled', $filters['is_controlled']);
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

    public function getExpiring(int $institutionId, int $days = 30): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->active()
            ->whereHas('batches', function ($q) use ($days) {
                $q->active()
                  ->where('expiry_date', '<=', now()->addDays($days))
                  ->where('expiry_date', '>', now());
            })
            ->with('batches')
            ->get();
    }

    public function updateStock(int $id, int $quantity, string $type): Medication
    {
        $medication = $this->model->findOrFail($id);

        if ($type === 'increase') {
            $medication->increment('current_stock', $quantity);
        } else {
            $medication->decrement('current_stock', $quantity);
        }

        return $medication->fresh();
    }
}
