<?php

namespace App\Repositories\Eloquent;

use App\Models\Diagnosis;
use App\Repositories\Interfaces\DiagnosisRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentDiagnosisRepository implements DiagnosisRepositoryInterface
{
    public function __construct(
        protected Diagnosis $model,
    ) {}

    public function findById(int $id): ?Diagnosis
    {
        return $this->model->with('medicalRecord', 'cie10Code')->find($id);
    }

    public function create(array $data): Diagnosis
    {
        return $this->model->create($data);
    }

    public function delete(int $id): bool
    {
        $diagnosis = $this->model->findOrFail($id);

        return $diagnosis->delete();
    }

    public function listByMedicalRecord(int $medicalRecordId): Collection
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->with('cie10Code')
            ->get();
    }

    public function getTopDiagnoses(int $institutionId, int $limit = 10): Collection
    {
        return $this->model
            ->whereHas('medicalRecord', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->whereNotNull('cie10_code_id')
            ->select('cie10_code_id', \DB::raw('COUNT(*) as total'))
            ->groupBy('cie10_code_id')
            ->with('cie10Code')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
