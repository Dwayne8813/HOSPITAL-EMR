<?php

namespace App\Repositories\Interfaces;

use App\Models\MedicalRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MedicalRecordRepositoryInterface
{
    public function findById(int $id): ?MedicalRecord;

    public function create(array $data): MedicalRecord;

    public function update(int $id, array $data): MedicalRecord;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function getWithRelations(int $id): ?MedicalRecord;

    public function countByInstitution(int $institutionId): int;

    public function getRecentRecords(int $institutionId, int $limit = 10): Collection;
}
