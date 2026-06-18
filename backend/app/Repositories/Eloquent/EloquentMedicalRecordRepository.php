<?php

namespace App\Repositories\Eloquent;

use App\Models\MedicalRecord;
use App\Repositories\Interfaces\MedicalRecordRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentMedicalRecordRepository implements MedicalRecordRepositoryInterface
{
    public function __construct(
        protected MedicalRecord $model,
    ) {}

    public function findById(int $id): ?MedicalRecord
    {
        return $this->model->with('patient', 'doctor', 'institution')->find($id);
    }

    public function create(array $data): MedicalRecord
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): MedicalRecord
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);

        return $record;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('doctor', 'diagnoses')
            ->latest('visit_date')
            ->paginate($perPage);
    }

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byDoctor($doctorId)
            ->with('patient', 'diagnoses')
            ->latest('visit_date')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'diagnoses');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->byDoctor($filters['doctor_id']);
        }

        if (!empty($filters['patient_id'])) {
            $query->byPatient($filters['patient_id']);
        }

        return $query->latest('visit_date')->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor')
            ->whereBetween('visit_date', [$from, $to])
            ->latest('visit_date')
            ->paginate($perPage);
    }

    public function getWithRelations(int $id): ?MedicalRecord
    {
        return $this->model
            ->with([
                'antecedent',
                'consultation',
                'diagnoses.cie10Code',
                'soapNote',
                'evolutions.doctor',
                'vitalSigns.recordedBy',
            ])
            ->find($id);
    }

    public function countByInstitution(int $institutionId): int
    {
        return $this->model->byInstitution($institutionId)->count();
    }

    public function getRecentRecords(int $institutionId, int $limit = 10): Collection
    {
        return $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor')
            ->latest('visit_date')
            ->limit($limit)
            ->get();
    }
}
