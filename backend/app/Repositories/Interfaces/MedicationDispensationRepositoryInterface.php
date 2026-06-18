<?php

namespace App\Repositories\Interfaces;

use App\Models\MedicationDispensation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MedicationDispensationRepositoryInterface
{
    public function findById(int $id): ?MedicationDispensation;

    public function create(array $data): MedicationDispensation;

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator;

    public function listByMedication(int $medicationId, int $perPage = 15): LengthAwarePaginator;

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator;
}
