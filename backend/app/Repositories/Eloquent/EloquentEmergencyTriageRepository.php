<?php

namespace App\Repositories\Eloquent;

use App\Models\EmergencyTriage;
use App\Repositories\Interfaces\EmergencyTriageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentEmergencyTriageRepository implements EmergencyTriageRepositoryInterface
{
    public function __construct(
        protected EmergencyTriage $model,
    ) {}

    public function findById(int $id): ?EmergencyTriage
    {
        return $this->model->with('patient', 'doctor', 'institution', 'record')->find($id);
    }

    public function create(array $data): EmergencyTriage
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): EmergencyTriage
    {
        $triage = $this->model->findOrFail($id);
        $triage->update($data);

        return $triage;
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['level'])) {
            $query->byLevel($filters['level']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('arrival_time', $filters['date']);
        }

        return $query->orderBy('arrival_time', 'desc')->paginate($perPage);
    }

    public function getWaiting(int $institutionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->pending()
            ->with('patient')
            ->orderBy('arrival_time')
            ->paginate($perPage);
    }

    public function getByLevel(int $institutionId, string $level, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->byLevel($level)
            ->with('patient', 'doctor')
            ->orderBy('arrival_time', 'desc')
            ->paginate($perPage);
    }

    public function getTodaysPatients(int $institutionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereDate('arrival_time', today())
            ->with('patient', 'doctor')
            ->orderBy('arrival_time')
            ->paginate($perPage);
    }
}
