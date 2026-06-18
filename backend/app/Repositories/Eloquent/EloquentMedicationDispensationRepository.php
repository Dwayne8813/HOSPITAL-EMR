<?php

namespace App\Repositories\Eloquent;

use App\Models\MedicationDispensation;
use App\Repositories\Interfaces\MedicationDispensationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentMedicationDispensationRepository implements MedicationDispensationRepositoryInterface
{
    public function __construct(
        protected MedicationDispensation $model,
    ) {}

    public function findById(int $id): ?MedicationDispensation
    {
        return $this->model->with('patient', 'medication', 'batch', 'dispensedBy', 'prescriptionItem')->find($id);
    }

    public function create(array $data): MedicationDispensation
    {
        return $this->model->create($data);
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('medication', 'dispensedBy')
            ->orderBy('dispensed_at', 'desc')
            ->paginate($perPage);
    }

    public function listByMedication(int $medicationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('medication_id', $medicationId)
            ->with('patient', 'dispensedBy')
            ->orderBy('dispensed_at', 'desc')
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('medication', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->whereBetween('dispensed_at', [$from, $to])
            ->with('patient', 'medication', 'dispensedBy')
            ->orderBy('dispensed_at', 'desc')
            ->paginate($perPage);
    }
}
