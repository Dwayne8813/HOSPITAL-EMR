<?php

namespace App\Repositories\Interfaces;

use App\Models\Consultation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ConsultationRepositoryInterface
{
    public function findById(int $id): ?Consultation;

    public function create(array $data): Consultation;

    public function update(int $id, array $data): Consultation;

    public function listByMedicalRecord(int $medicalRecordId, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;
}
