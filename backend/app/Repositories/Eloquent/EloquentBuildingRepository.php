<?php

namespace App\Repositories\Eloquent;

use App\Models\Building;
use App\Repositories\Interfaces\BuildingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentBuildingRepository implements BuildingRepositoryInterface
{
    public function __construct(
        protected Building $model,
    ) {}

    public function findById(int $id): ?Building
    {
        return $this->model->with('institution')->find($id);
    }

    public function create(array $data): Building
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Building
    {
        $building = $this->model->findOrFail($id);
        $building->update($data);

        return $building;
    }

    public function delete(int $id): bool
    {
        $building = $this->model->findOrFail($id);

        return $building->delete();
    }

    public function list(int $institutionId, array $filters = []): Collection
    {
        $query = $this->model->byInstitution($institutionId);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->get();
    }

    public function getWithRooms(int $id): ?Building
    {
        return $this->model
            ->with(['rooms' => function ($q) {
                $q->orderBy('number');
            }])
            ->find($id);
    }
}
