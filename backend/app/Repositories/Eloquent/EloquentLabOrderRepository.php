<?php

namespace App\Repositories\Eloquent;

use App\Models\LabOrder;
use App\Repositories\Interfaces\LabOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentLabOrderRepository implements LabOrderRepositoryInterface
{
    public function __construct(
        protected LabOrder $model,
    ) {}

    public function findById(int $id): ?LabOrder
    {
        return $this->model->with('patient', 'doctor', 'institution', 'medicalRecord')->find($id);
    }

    public function create(array $data): LabOrder
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): LabOrder
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);

        return $order;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('doctor', 'items')
            ->orderBy('ordered_at', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'items');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        return $query->orderBy('ordered_at', 'desc')->paginate($perPage);
    }

    public function getWithItems(int $id): ?LabOrder
    {
        return $this->model
            ->with(['patient', 'doctor', 'items.results', 'specimen'])
            ->find($id);
    }

    public function getByStatus(int $institutionId, string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->where('status', $status)
            ->with('patient', 'doctor')
            ->orderBy('ordered_at', 'desc')
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('ordered_at', [$from, $to])
            ->with('patient', 'doctor')
            ->orderBy('ordered_at', 'desc')
            ->paginate($perPage);
    }
}
