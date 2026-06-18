<?php

namespace App\Repositories\Eloquent;

use App\Models\Room;
use App\Repositories\Interfaces\RoomRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentRoomRepository implements RoomRepositoryInterface
{
    public function __construct(
        protected Room $model,
    ) {}

    public function findById(int $id): ?Room
    {
        return $this->model->with('floor', 'institution', 'beds')->find($id);
    }

    public function create(array $data): Room
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Room
    {
        $room = $this->model->findOrFail($id);
        $room->update($data);

        return $room;
    }

    public function listByBuilding(int $buildingId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('floor', function ($q) use ($buildingId) {
                $q->where('building_id', $buildingId);
            })
            ->with('beds')
            ->orderBy('number')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->byInstitution($institutionId)->with('floor', 'beds');

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('number')->paginate($perPage);
    }

    public function getAvailable(int $institutionId): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->available()
            ->with('beds')
            ->get();
    }

    public function getByType(int $institutionId, string $type): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byType($type)
            ->with('beds')
            ->get();
    }
}
