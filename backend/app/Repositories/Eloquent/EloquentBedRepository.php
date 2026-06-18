<?php

namespace App\Repositories\Eloquent;

use App\Models\Bed;
use App\Repositories\Interfaces\BedRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentBedRepository implements BedRepositoryInterface
{
    public function __construct(
        protected Bed $model,
    ) {}

    public function findById(int $id): ?Bed
    {
        return $this->model->with('room', 'institution', 'hospitalization')->find($id);
    }

    public function create(array $data): Bed
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Bed
    {
        $bed = $this->model->findOrFail($id);
        $bed->update($data);

        return $bed;
    }

    public function listByRoom(int $roomId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byRoom($roomId)
            ->with('hospitalization.patient')
            ->orderBy('number')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('room');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['room_id'])) {
            $query->byRoom($filters['room_id']);
        }

        return $query->orderBy('number')->paginate($perPage);
    }

    public function getAvailable(int $institutionId): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->available()
            ->with('room')
            ->get();
    }

    public function getByStatus(int $institutionId, string $status): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->where('status', $status)
            ->with('room')
            ->get();
    }

    public function getOccupancyMap(int $institutionId): array
    {
        $rooms = $this->model
            ->byInstitution($institutionId)
            ->with('room')
            ->get();

        $total = $rooms->count();
        $available = $rooms->where('status', 'available')->count();
        $occupied = $rooms->where('status', 'occupied')->count();
        $maintenance = $rooms->where('status', 'maintenance')->count();

        return [
            'total' => $total,
            'available' => $available,
            'occupied' => $occupied,
            'maintenance' => $maintenance,
            'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 2) : 0,
            'by_type' => $rooms->groupBy('type')->map(fn ($items) => [
                'total' => $items->count(),
                'available' => $items->where('status', 'available')->count(),
                'occupied' => $items->where('status', 'occupied')->count(),
            ])->toArray(),
        ];
    }
}
