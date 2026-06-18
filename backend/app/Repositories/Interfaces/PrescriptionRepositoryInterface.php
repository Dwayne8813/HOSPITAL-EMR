<?php

namespace App\Repositories\Interfaces;

use App\Models\Prescription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PrescriptionRepositoryInterface
{
    public function findById(int $id): ?Prescription;

    public function create(array $data): Prescription;

    public function update(int $id, array $data): Prescription;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function listByMedicalRecord(int $medicalRecordId): LengthAwarePaginator;

    public function getWithItems(int $id): ?Prescription;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;

    public function countDispensed(int $institutionId, ?string $from = null, ?string $to = null): int;
}
