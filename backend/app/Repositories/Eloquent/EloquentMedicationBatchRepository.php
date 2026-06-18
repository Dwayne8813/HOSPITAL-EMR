<?php

namespace App\Repositories\Eloquent;

use App\Models\MedicationBatch;
use App\Repositories\Interfaces\MedicationBatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentMedicationBatchRepository implements MedicationBatchRepositoryInterface
{
    public function __construct(
        protected MedicationBatch $model,
    ) {}

    public function findById(int $id): ?MedicationBatch
    {
        return $this->model->with('medication')->find($id);
    }

    public function create(array $data): MedicationBatch
    {
        return $this->model->create($data);
    }

    public function listByMedication(int $medicationId): Collection
    {
        return $this->model
            ->where('medication_id', $medicationId)
            ->active()
            ->orderBy('expiry_date')
            ->get();
    }

    public function getExpiringSoon(int $institutionId, int $days = 30): Collection
    {
        return $this->model
            ->whereHas('medication', function ($q) use ($institutionId) {
                $q->where('institution_id', $institutionId);
            })
            ->expiringSoon($days)
            ->with('medication')
            ->get();
    }

    public function getByBatchNumber(string $batchNumber): ?MedicationBatch
    {
        return $this->model->where('batch_number', $batchNumber)->with('medication')->first();
    }
}
