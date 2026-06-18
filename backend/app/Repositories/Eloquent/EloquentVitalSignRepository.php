<?php

namespace App\Repositories\Eloquent;

use App\Models\VitalSign;
use App\Repositories\Interfaces\VitalSignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentVitalSignRepository implements VitalSignRepositoryInterface
{
    public function __construct(
        protected VitalSign $model,
    ) {}

    public function findById(int $id): ?VitalSign
    {
        return $this->model->with('medicalRecord', 'patient', 'recordedBy')->find($id);
    }

    public function create(array $data): VitalSign
    {
        return $this->model->create($data);
    }

    public function listByPatient(int $patientId, int $limit = 50): Collection
    {
        return $this->model
            ->byPatient($patientId)
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function listByMedicalRecord(int $medicalRecordId): Collection
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    public function getLatest(int $patientId): ?VitalSign
    {
        return $this->model
            ->byPatient($patientId)
            ->with('recordedBy')
            ->latest('recorded_at')
            ->first();
    }

    public function getHistory(int $patientId, string $from, string $to): Collection
    {
        return $this->model
            ->byPatient($patientId)
            ->with('recordedBy')
            ->whereBetween('recorded_at', [$from, $to])
            ->orderBy('recorded_at', 'desc')
            ->get();
    }
}
