<?php

namespace App\Repositories\Interfaces;

use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PatientRepositoryInterface
{
    public function findById(int $id): ?Patient;

    public function findByCI(string $documentNumber): ?Patient;

    public function findByMedicalRecord(string $medicalRecordNumber): ?Patient;

    public function create(array $data): Patient;

    public function update(int $id, array $data): Patient;

    public function delete(int $id): bool;

    public function list(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function search(int $institutionId, string $query, int $perPage = 15): LengthAwarePaginator;

    public function getByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator;

    public function countByInstitution(int $institutionId): int;

    public function getRecentPatients(int $institutionId, int $limit = 10): Collection;

    public function getPatientWithDetails(int $id): ?Patient;
}
