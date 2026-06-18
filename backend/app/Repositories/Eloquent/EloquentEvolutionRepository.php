<?php

namespace App\Repositories\Eloquent;

use App\Models\Evolution;
use App\Repositories\Interfaces\EvolutionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentEvolutionRepository implements EvolutionRepositoryInterface
{
    public function __construct(
        protected Evolution $model,
    ) {}

    public function findById(int $id): ?Evolution
    {
        return $this->model->with('medicalRecord', 'doctor')->find($id);
    }

    public function create(array $data): Evolution
    {
        return $this->model->create($data);
    }

    public function listByMedicalRecord(int $medicalRecordId): Collection
    {
        return $this->model
            ->where('medical_record_id', $medicalRecordId)
            ->with('doctor')
            ->orderBy('evolution_date', 'desc')
            ->get();
    }

    public function getByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->with('medicalRecord.patient')
            ->latest('evolution_date')
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('medicalRecord', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->whereBetween('evolution_date', [$from, $to])
            ->with('medicalRecord.patient', 'doctor')
            ->latest('evolution_date')
            ->paginate($perPage);
    }
}
