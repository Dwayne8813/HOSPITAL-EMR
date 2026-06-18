<?php

namespace App\Repositories\Eloquent;

use App\Models\ImagingOrder;
use App\Repositories\Interfaces\ImagingOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentImagingOrderRepository implements ImagingOrderRepositoryInterface
{
    public function __construct(
        protected ImagingOrder $model,
    ) {}

    public function findById(int $id): ?ImagingOrder
    {
        return $this->model->with('patient', 'doctor', 'institution', 'medicalRecord', 'result')->find($id);
    }

    public function create(array $data): ImagingOrder
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ImagingOrder
    {
        $order = $this->model->findOrFail($id);
        $order->update($data);

        return $order;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('patient_id', $patientId)
            ->with('doctor', 'result')
            ->orderBy('ordered_at', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'result');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['imaging_type'])) {
            $query->where('imaging_type', $filters['imaging_type']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        return $query->orderBy('ordered_at', 'desc')->paginate($perPage);
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
}
