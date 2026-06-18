<?php

namespace App\Repositories\Interfaces;

use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Collection;

interface DiagnosisRepositoryInterface
{
    public function findById(int $id): ?Diagnosis;

    public function create(array $data): Diagnosis;

    public function delete(int $id): bool;

    public function listByMedicalRecord(int $medicalRecordId): Collection;

    public function getTopDiagnoses(int $institutionId, int $limit = 10): Collection;
}
