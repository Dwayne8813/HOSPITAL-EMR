<?php

namespace App\Repositories\Interfaces;

use App\Models\MedicationBatch;
use Illuminate\Database\Eloquent\Collection;

interface MedicationBatchRepositoryInterface
{
    public function findById(int $id): ?MedicationBatch;

    public function create(array $data): MedicationBatch;

    public function listByMedication(int $medicationId): Collection;

    public function getExpiringSoon(int $institutionId, int $days = 30): Collection;

    public function getByBatchNumber(string $batchNumber): ?MedicationBatch;
}
