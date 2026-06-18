<?php

namespace App\Repositories\Interfaces;

use App\Models\VitalSign;
use Illuminate\Database\Eloquent\Collection;

interface VitalSignRepositoryInterface
{
    public function findById(int $id): ?VitalSign;

    public function create(array $data): VitalSign;

    public function listByPatient(int $patientId, int $limit = 50): Collection;

    public function listByMedicalRecord(int $medicalRecordId): Collection;

    public function getLatest(int $patientId): ?VitalSign;

    public function getHistory(int $patientId, string $from, string $to): Collection;
}
