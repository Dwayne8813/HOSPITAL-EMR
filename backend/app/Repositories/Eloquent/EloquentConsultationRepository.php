<?php

namespace App\Repositories\Eloquent;

use App\Models\Consultation;
use App\Repositories\Interfaces\ConsultationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentConsultationRepository implements ConsultationRepositoryInterface
{
    public function __construct(
        protected Consultation $model,
    ) {}

    public function findById(int $id): ?Consultation
    {
        return $this->model->with('medicalRecord')->find($id);
    }

    public function create(array $data): Consultation
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Consultation
    {
        $consultation = $this->model->findOrFail($id);
        $consultation->update($data);

        return $consultation;
    }

    public function listByMedicalRecord(int $medicalRecordId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->latest()
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('medicalRecord', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->whereHas('medicalRecord')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate($perPage);
    }
}
